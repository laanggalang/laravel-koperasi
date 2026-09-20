<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'member_no','name','nik','phone','email','address','join_date','exit_date','status'
    ];

    protected $casts = ['join_date' => 'date', 'exit_date' => 'date'];

    public function savings(): HasMany { return $this->hasMany(Saving::class); }
    public function loans(): HasMany { return $this->hasMany(Loan::class); }

    public function hasUnpaidLoans(): bool
    {
        return $this->loans()->whereIn('status', ['pending', 'active'])->exists();
    }
}
