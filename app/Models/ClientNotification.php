<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientNotification extends Model
{
    protected $fillable = ['user_id', 'type', 'icon', 'title', 'body', 'data', 'read_at'];

    protected $casts = [
        'data'     => 'array',
        'read_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public static function forUser(int $userId, string $type, string $title, string $body, array $data = []): self
    {
        $iconMap = [
            'transfer'    => 'paper-plane',
            'loan_update' => 'file-contract',
            'credit'      => 'circle-plus',
            'debit'       => 'circle-minus',
            'system'      => 'bell',
        ];

        return self::create([
            'user_id' => $userId,
            'type'    => $type,
            'icon'    => $iconMap[$type] ?? 'bell',
            'title'   => $title,
            'body'    => $body,
            'data'    => $data ?: null,
        ]);
    }
}
