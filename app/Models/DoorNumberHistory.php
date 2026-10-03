<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoorNumberHistory extends Model
{
    public const ACTION_REGISTERED  = 'registered';
    public const ACTION_TRANSFERRED = 'transferred';
    public const ACTION_EXCHANGED   = 'exchanged';
    public const ACTION_RELEASED    = 'released';
    public const ACTION_DEACTIVATED = 'deactivated';
    public const ACTION_ACTIVATED   = 'activated';

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid ??= (string) \Illuminate\Support\Str::uuid();
        });
    }

    protected $fillable = [
        'door_number_id', 'from_member_id', 'to_member_id', 'action',
        'action_date', 'notes', 'performed_by',
    ];

    protected $casts = ['action_date' => 'date'];

    public function doorNumber(): BelongsTo { return $this->belongsTo(DoorNumber::class); }
    public function fromMember(): BelongsTo { return $this->belongsTo(Member::class, 'from_member_id'); }
    public function toMember(): BelongsTo { return $this->belongsTo(Member::class, 'to_member_id'); }
    public function performer(): BelongsTo { return $this->belongsTo(User::class, 'performed_by'); }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            self::ACTION_REGISTERED  => 'Didaftarkan',
            self::ACTION_TRANSFERRED => 'Ditransfer',
            self::ACTION_EXCHANGED   => 'Dipertukarkan',
            self::ACTION_RELEASED    => 'Dilepas ke KBP',
            self::ACTION_DEACTIVATED => 'Dinonaktifkan',
            self::ACTION_ACTIVATED   => 'Diaktifkan',
            default                  => $this->action,
        };
    }
}
