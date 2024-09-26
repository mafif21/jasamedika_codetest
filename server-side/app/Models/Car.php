<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;
    protected $fillable = ['brand', 'model', 'plate_number', 'price_rate', 'is_available'];

    public function rents(): HasMany
    {
        return $this->hasMany(Rent::class, 'rent_id');
    }
}
