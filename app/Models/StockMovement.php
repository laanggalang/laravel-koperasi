<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';
    public const TYPE_ADJUST = 'adjust';

    protected $fillable = [
        'sparepart_id', 'type', 'qty', 'stock_before', 'stock_after',
        'reference', 'notes', 'performed_by',
    ];

    protected $casts = [
        'qty'          => 'decimal:2',
        'stock_before' => 'decimal:2',
        'stock_after'  => 'decimal:2',
    ];

    public function sparepart(): BelongsTo { return $this->belongsTo(Sparepart::class); }
    public function performer(): BelongsTo { return $this->belongsTo(User::class, 'performed_by'); }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_IN     => 'Stok Masuk',
            self::TYPE_OUT    => 'Stok Keluar',
            default           => 'Penyesuaian',
        };
    }
}
