<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    public const STATUS_PAID = 'paid';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_DEBT = 'debt';

    public const BUYER_ANGGOTA = 'anggota';
    public const BUYER_DRIVER = 'driver';
    public const BUYER_UMUM = 'umum';

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid ??= (string) \Illuminate\Support\Str::uuid();
            $model->invoice_no ??= 'INV-' . now()->format('YmdHis') . '-' . strtoupper(\Illuminate\Support\Str::random(4));
        });
    }

    protected $fillable = [
        'invoice_no', 'buyer_type', 'member_id', 'driver_name',
        'total', 'total_paid', 'change_amount', 'status',
        'sale_date', 'notes', 'performed_by',
    ];

    protected $casts = [
        'total'         => 'decimal:2',
        'total_paid'    => 'decimal:2',
        'change_amount' => 'decimal:2',
        'sale_date'     => 'date',
    ];

    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function performer(): BelongsTo { return $this->belongsTo(User::class, 'performed_by'); }
    public function items(): HasMany { return $this->hasMany(SaleItem::class); }
    public function payments(): HasMany { return $this->hasMany(SalePayment::class); }

    public function getBuyerDisplayAttribute(): string
    {
        return match ($this->buyer_type) {
            self::BUYER_ANGGOTA => $this->member?->name ?? '-',
            self::BUYER_DRIVER  => $this->driver_name ?? '-',
            default             => 'Umum',
        };
    }

    public function getBuyerLabelAttribute(): string
    {
        return match ($this->buyer_type) {
            self::BUYER_ANGGOTA => 'Anggota',
            self::BUYER_DRIVER  => 'Driver',
            default             => 'Umum',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PAID   => 'Lunas',
            self::STATUS_PARTIAL => 'Bayar Sebagian',
            default             => 'Utang',
        };
    }

    public function getRemainingDebtAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->total_paid);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('invoice_no', 'like', "%{$term}%")
              ->orWhere('driver_name', 'like', "%{$term}%")
              ->orWhereHas('member', fn ($m) => $m->where('name', 'like', "%{$term}%"));
        });
    }
}
