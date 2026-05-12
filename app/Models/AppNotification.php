<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'target_user_id',
        'target_wilayah_id',
        'target_roles',
        'title',
        'message',
        'metadata',
        'sent_at',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id')->withTrashed();
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id')->withTrashed();
    }

    public function targetWilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'target_wilayah_id');
    }
}
