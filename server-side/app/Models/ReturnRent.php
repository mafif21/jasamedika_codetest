<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRent extends Model
{
    use HasFactory;
    protected $fillable = ['rent_id', 'plate_number', 'total_days', 'total_cost'];

    public function rent(): BelongsTo
    {
        return $this->belongsTo(Rent::class, 'rent_id');
    }
}
