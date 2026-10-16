<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'sparepart_id', 'nama_snapshot', 'qty', 'harga_satuan', 'subtotal',
    ];

    protected $casts = [
        'qty'          => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal'     => 'decimal:2',
    ];

    public function sale(): BelongsTo { return $this->belongsTo(Sale::class); }
    public function sparepart(): BelongsTo { return $this->belongsTo(Sparepart::class); }
}
