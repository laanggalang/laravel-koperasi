<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
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
        'member_id','loan_no','principal','interest_rate','tenor',
        'monthly_payment','total_payment','remaining_balance',
        'start_date','status','notes'
    ];

    protected $casts = [
        'principal'=>'decimal:2','interest_rate'=>'decimal:2',
        'monthly_payment'=>'decimal:2','total_payment'=>'decimal:2',
        'remaining_balance'=>'decimal:2','start_date'=>'date'
    ];

    public function member(): BelongsTo { return $this->belongsTo(Member::class); }
    public function installments(): HasMany { return $this->hasMany(Installment::class); }
}
