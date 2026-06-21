<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminNotification extends Model
{
    protected $fillable = ['admin_id', 'type', 'icon', 'title', 'body', 'data', 'read_at'];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public static function forAdmin(int $adminId, string $type, string $title, string $body, array $data = []): self
    {
        $iconMap = [
            'support'  => 'comments',
            'transfer' => 'exchange-alt',
            'system'   => 'bell',
        ];

        return self::create([
            'admin_id' => $adminId,
            'type'     => $type,
            'icon'     => $iconMap[$type] ?? 'bell',
            'title'    => $title,
            'body'     => $body,
            'data'     => $data ?: null,
        ]);
    }

    /**
     * Notify all super-admins + the responsible admin of a client.
     */
    public static function notifyAdminsForClient(User $client, string $type, string $title, string $body, array $data = []): void
    {
        $notified = [];

        // Responsible admin (created_by)
        if ($client->created_by) {
            self::forAdmin($client->created_by, $type, $title, $body, $data);
            $notified[] = $client->created_by;
        }

        // Admin of any loan
        $loanAdminId = $client->clientLoans()->whereNotNull('admin_id')->value('admin_id');
        if ($loanAdminId && ! in_array($loanAdminId, $notified)) {
            self::forAdmin($loanAdminId, $type, $title, $body, $data);
            $notified[] = $loanAdminId;
        }

        // Super-admins (if no specific admin found or always)
        if (empty($notified)) {
            User::role('super-admin')->each(function (User $sa) use ($type, $title, $body, $data) {
                self::forAdmin($sa->id, $type, $title, $body, $data);
            });
        }
    }
}
