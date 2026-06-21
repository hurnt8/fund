<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\ClientNotification;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
// str_starts_with() used directly (PHP 8+)

class SupportController extends Controller
{
    public function index()
    {
        $auth         = Auth::user();
        $isSuperAdmin = $auth->hasRole('super-admin');

        $query = User::where('type', 'client')->whereHas('supportMessages');

        if (! $isSuperAdmin) {
            $adminId = $auth->id;
            $query->where(function ($q) use ($adminId) {
                $q->where('created_by', $adminId)
                  ->orWhereHas('clientLoans', fn ($q2) => $q2->where('admin_id', $adminId));
            });
        }

        $clients = $query->with(['supportMessages' => fn ($q) => $q->latest()->limit(1)])
            ->get()
            ->map(function (User $c) {
                $c->last_message     = $c->supportMessages->first();
                $c->unread_for_admin = SupportMessage::where('client_id', $c->id)
                    ->where('sender_type', 'client')
                    ->whereNull('read_at')
                    ->count();
                return $c;
            })
            ->sortByDesc(fn ($c) => optional($c->last_message)->created_at);

        return view('admin.support.index', compact('clients', 'isSuperAdmin'));
    }

    public function show(User $client)
    {
        $this->authorizeClient($client);

        $messages = SupportMessage::where('client_id', $client->id)->oldest()->get();

        SupportMessage::where('client_id', $client->id)
            ->where('sender_type', 'client')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        AdminNotification::where('admin_id', Auth::id())
            ->where('type', 'support')
            ->whereJsonContains('data->client_id', $client->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $lastId   = $messages->last()?->id ?? 0;
        $chatData = $messages->map(fn ($m) => $m->toChat());

        return view('admin.support.show', compact('client', 'chatData', 'lastId'));
    }

    public function poll(Request $request, User $client)
    {
        $this->authorizeClient($client);
        $after = (int) $request->query('after', 0);

        $messages = SupportMessage::where('client_id', $client->id)
            ->where('id', '>', $after)
            ->oldest()
            ->get();

        $messages->where('sender_type', 'client')
            ->each(fn ($m) => $m->update(['read_at' => now()]));

        return response()->json([
            'messages' => $messages->map(fn ($m) => $m->toChat()),
        ]);
    }

    public function store(Request $request, User $client)
    {
        $this->authorizeClient($client);
        $request->validate([
            'body' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:10240',
        ]);

        if (! $request->filled('body') && ! $request->hasFile('file')) {
            return response()->json(['error' => 'Message vide'], 422);
        }

        $filePath = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $mime     = $file->getMimeType();
            $fileType = str_starts_with($mime, 'image/') ? 'image' : 'audio';
            $filePath = $file->store("support/{$client->id}", 'public');
        }

        $msg = SupportMessage::create([
            'client_id'   => $client->id,
            'sender_type' => 'admin',
            'body'        => $request->body,
            'file_path'   => $filePath,
            'file_type'   => $fileType,
        ]);

        $preview = $msg->body
            ? Str::limit($msg->body, 80)
            : ($msg->file_type === 'image' ? '📷 Image' : '🎤 Audio');

        ClientNotification::forUser($client->id, 'system', 'Réponse de votre conseiller', $preview, ['type' => 'support']);

        return response()->json(['message' => $msg->toChat()]);
    }

    private function authorizeClient(User $client): void
    {
        $auth = Auth::user();
        if ($auth->hasRole('super-admin')) return;
        $adminId   = $auth->id;
        $isManaged = $client->created_by === $adminId
            || $client->clientLoans()->where('admin_id', $adminId)->exists();
        abort_unless($isManaged, 403);
    }
}
