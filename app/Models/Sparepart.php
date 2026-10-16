<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sparepart extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const KATEGORI = ['oli', 'ban', 'aki', 'filter', 'spion', 'lainnya'];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->uuid ??= (string) \Illuminate\Support\Str::uuid();
            $model->kode ??= 'SP-' . strtoupper(\Illuminate\Support\Str::random(6));
        });
    }

    protected $fillable = [
        'kode', 'nama', 'kategori', 'satuan',
        'harga_beli', 'harga_anggota', 'harga_driver', 'harga_umum',
        'stok_qty', 'stok_minimal', 'status',
    ];

    protected $casts = [
        'harga_beli'    => 'decimal:2',
        'harga_anggota' => 'decimal:2',
        'harga_driver'  => 'decimal:2',
        'harga_umum'    => 'decimal:2',
        'stok_qty'      => 'decimal:2',
        'stok_minimal'  => 'decimal:2',
    ];

    public function saleItems(): HasMany { return $this->hasMany(SaleItem::class); }
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->orderByDesc('created_at');
    }

    public function isActive(): bool { return $this->status === self::STATUS_ACTIVE; }

    public function isLowStock(): bool
    {
        return (float) $this->stok_qty <= (float) $this->stok_minimal;
    }

    /**
     * Harga sesuai tipe pembeli.
     */
    public function priceFor(string $buyerType): float
    {
        return (float) match ($buyerType) {
            'anggota' => $this->harga_anggota,
            'driver'  => $this->harga_driver,
            default   => $this->harga_umum,
        };
    }

    /**
     * Nilai stok (HPP x qty) untuk stat card.
     */
    public function getStockValueAttribute(): float
    {
        return (float) $this->harga_beli * (float) $this->stok_qty;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;

        return $query->where(function ($q) use ($term) {
            $q->where('nama', 'like', "%{$term}%")
              ->orWhere('kode', 'like', "%{$term}%")
              ->orWhere('kategori', 'like', "%{$term}%");
        });
    }
}
