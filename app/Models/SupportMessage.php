<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SupportMessage extends Model
{
    protected $fillable = ['client_id', 'sender_type', 'body', 'file_path', 'file_type', 'is_bot', 'read_at'];

    protected $casts = ['read_at' => 'datetime', 'is_bot' => 'boolean'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function isFromClient(): bool
    {
        return $this->sender_type === 'client';
    }

    public function fileUrl(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    public function toChat(): array
    {
        $isToday     = $this->created_at->isToday();
        $isYesterday = $this->created_at->isYesterday();

        return [
            'id'          => $this->id,
            'sender_type' => $this->sender_type,
            'is_bot'      => (bool) $this->is_bot,
            'body'        => $this->body,
            'file_url'    => $this->fileUrl(),
            'file_type'   => $this->file_type,
            'time'        => $this->created_at->format('H:i'),
            'date_key'    => $this->created_at->format('Y-m-d'),
            'date_label'  => $isToday ? "Aujourd'hui" : ($isYesterday ? 'Hier' : $this->created_at->isoFormat('D MMM YYYY')),
        ];
    }
}
