<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoorNumber extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_INACTIVE = 'inactive';

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
        'member_id', 'door_no', 'driver_name', 'plate_no', 'vehicle_type',
        'status', 'registered_date', 'deactivated_date',
    ];

    protected $casts = [
        'registered_date' => 'date',
        'deactivated_date' => 'date',
    ];

    public function member(): BelongsTo { return $this->belongsTo(Member::class); }

    public function histories(): HasMany
    {
        return $this->hasMany(DoorNumberHistory::class)->orderByDesc('action_date')->orderByDesc('id');
    }

    /**
     * Nama pemegang untuk display: KBP saat tersedia (member_id NULL), anggota sendiri, atau nama pengemudi.
     */
    public function getHolderDisplayAttribute(): string
    {
        if ($this->status === self::STATUS_AVAILABLE || !$this->member) {
            return 'KBP (Koperasi)';
        }

        return $this->member->name;
    }

    /**
     * Nama pengemudi display: pengemudi eksternal, atau "(Anggota sendiri)".
     */
    public function getDriverDisplayAttribute(): string
    {
        return $this->driver_name ?? 'Anggota sendiri';
    }

    /**
     * GUARD: apakah seluruh hak & kewajiban nomor pintu ini sudah selesai?
     *
     * PLACEHOLDER: untuk saat ini belum ada entitas iuran/sewa/denda nomor pintu,
     * jadi selalu true. Nanti kalau entitas tagihan NP dibuat (mis. iuran bulanan,
     * denda keterlambatan), isi logikanya di sini — semua guard transfer/nonaktif/lepas
     * memanggil method ini, jadi cukup ubah satu titik ini saja.
     */
    public function hasSettledObligations(): bool
    {
        return true;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('door_no', 'like', "%{$term}%")
              ->orWhere('plate_no', 'like', "%{$term}%")
              ->orWhere('driver_name', 'like', "%{$term}%")
              ->orWhere('vehicle_type', 'like', "%{$term}%")
              ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$term}%"))
              ->orWhereHas('member', fn ($m) => $m->where('member_no', 'like', "%{$term}%"));
        });
    }

    public function isActive(): bool { return $this->status === self::STATUS_ACTIVE; }
    public function isAvailable(): bool { return $this->status === self::STATUS_AVAILABLE; }
    public function isInactive(): bool { return $this->status === self::STATUS_INACTIVE; }
}
