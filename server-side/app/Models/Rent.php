<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rent extends Model
{
    use HasFactory;
    protected $fillable = ['start_date', 'end_date', 'user_id', 'car_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, "car_id");
    }

    public function return_rent(): HasOne
    {
        return $this->hasOne(ReturnRent::class, "return_rent_id");
    }
}
