<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
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
        'member_no','name','nik','phone','email','address','join_date','exit_date','status'
    ];

    protected $casts = ['join_date' => 'date', 'exit_date' => 'date'];

    public function savings(): HasMany { return $this->hasMany(Saving::class); }
    public function loans(): HasMany { return $this->hasMany(Loan::class); }

    public function hasUnpaidLoans(): bool
    {
        return $this->loans()->whereIn('status', ['pending', 'active'])->exists();
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('member_no', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }
}
