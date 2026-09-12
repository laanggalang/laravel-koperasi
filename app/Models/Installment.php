<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Installment extends Model
{
    protected $fillable = ['loan_id','installment_no','amount','paid_date','notes'];
    protected $casts = ['paid_date'=>'date','amount'=>'decimal:2'];
    public function loan(): BelongsTo { return $this->belongsTo(Loan::class); }
}
