<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLab extends Model
{
    use HasFactory;

    protected $table = 'user_labs';

    protected $fillable = [
        'user_id',
        'creatine',
        'sodium',
        'potassium',
        'hemoglobin',
        'lactate',
        'glucose',
    ];

    protected $casts = [
        'creatine' => 'float',
        'sodium' => 'float',
        'potassium' => 'float',
        'hemoglobin' => 'float',
        'lactate' => 'float',
        'glucose' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
