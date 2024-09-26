<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class History extends Model
{
    use HasFactory;
    protected $fillable = ['rental_id', 'plat_number', 'total_days', 'total_cost'];

    public function rent(): BelongsTo
    {
        return $this->belongsTo(Rent::class, 'rent_id');
    }
}
