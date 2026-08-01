<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'balance', 'discount_percent'];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'discount_percent' => 'decimal:2',
        ];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}