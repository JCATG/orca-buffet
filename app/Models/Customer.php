<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'phone', 'city'])]
class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function whatsappNumber(): string
    {
        $digits = preg_replace('/\D/', '', (string) $this->phone);

        return strlen($digits) <= 11 ? '55'.$digits : $digits;
    }
}
