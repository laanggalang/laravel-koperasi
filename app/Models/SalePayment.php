<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalePayment extends Model
{
    public const METHOD_CASH = 'cash';
    public const METHOD_SIMPANAN = 'simpanan';
    public const METHOD_UTANG = 'utang';

    protected $fillable = ['sale_id', 'method', 'amount', 'reference'];

    protected $casts = ['amount' => 'decimal:2'];

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            self::METHOD_CASH     => 'Tunai',
            self::METHOD_SIMPANAN => 'Simpanan Sukarela',
            default               => 'Utang',
        };
    }
}
