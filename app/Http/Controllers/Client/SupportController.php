<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Mail\AdminSupportMail;
use App\Models\AdminNotification;
use App\Models\SupportMessage;
use App\Models\User;
use App\Services\SupportAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function __construct(private SupportAiService $ai) {}

    public function index()
    {
        $user     = Auth::user();
        $messages = SupportMessage::where('client_id', $user->id)->oldest()->get();

        SupportMessage::where('client_id', $user->id)
            ->where('sender_type', 'admin')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $lastId   = $messages->last()?->id ?? 0;
        $chatData = $messages->map(fn ($m) => $m->toChat());

        return view('client.app.support', compact('user', 'chatData', 'lastId'));
    }

    public function poll(Request $request)
    {
        $user  = Auth::user();
        $after = (int) $request->query('after', 0);

        $messages = SupportMessage::where('client_id', $user->id)
            ->where('id', '>', $after)
            ->oldest()
            ->get();

        $messages->where('sender_type', 'admin')
            ->each(fn ($m) => $m->update(['read_at' => now()]));

        return response()->json([
            'messages' => $messages->map(fn ($m) => $m->toChat()),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'body' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ]);

        if (! $request->filled('body') && ! $request->hasFile('file')) {
            return response()->json(['error' => 'Message vide'], 422);
        }

        $user     = Auth::user();
        $filePath = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $mime     = $file->getMimeType();
            $fileType = str_starts_with($mime, 'image/') ? 'image' : 'audio';
            $filePath = $file->store("support/{$user->id}", 'public');
        }

        // 1 — Save client message
        $msg = SupportMessage::create([
            'client_id'   => $user->id,
            'sender_type' => 'client',
            'body'        => $request->body,
            'file_path'   => $filePath,
            'file_type'   => $fileType,
        ]);

        // 2 — Notify human advisor
        $this->notifyAdmin($user, $msg);

        // 3 — AI auto-reply (text messages only; skip image-only uploads)
        if ($request->filled('body')) {
            $history = SupportMessage::where('client_id', $user->id)
                ->where('id', '<', $msg->id)
                ->oldest()
                ->get();

            $aiReply = $this->ai->generateReply($user, $history);

            if ($aiReply) {
                SupportMessage::create([
                    'client_id'   => $user->id,
                    'sender_type' => 'admin',
                    'is_bot'      => true,
                    'body'        => $aiReply,
                ]);
            }
        }

        return response()->json(['message' => $msg->toChat()]);
    }

    private function notifyAdmin(User $client, SupportMessage $msg): void
    {
        $adminIds = collect();
        if ($client->created_by) $adminIds->push($client->created_by);
        $loanAdminId = $client->clientLoans()->whereNotNull('admin_id')->value('admin_id');
        if ($loanAdminId) $adminIds->push($loanAdminId);
        $adminIds = $adminIds->unique();
        if ($adminIds->isEmpty()) $adminIds = User::role('super-admin')->pluck('id');

        $preview = $msg->body
            ? Str::limit($msg->body, 80)
            : ($msg->file_type === 'image' ? '📷 Image' : '🎤 Audio');

        foreach ($adminIds as $adminId) {
            AdminNotification::forAdmin($adminId, 'support', 'Message de ' . $client->name, $preview, ['client_id' => $client->id]);
            $admin = User::find($adminId);
            if ($admin) Mail::to($admin->email)->send(new AdminSupportMail($client, $msg));
        }
    }
}
