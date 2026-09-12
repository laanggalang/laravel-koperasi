<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Saving extends Model
{
    protected $fillable = ['member_id','type','amount','transaction_date','description'];
    protected $casts = ['transaction_date'=>'date','amount'=>'decimal:2'];
    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
}
