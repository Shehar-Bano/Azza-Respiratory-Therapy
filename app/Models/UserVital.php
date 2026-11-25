<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserVital extends Model
{
    use HasFactory;

    protected $table = 'user_vitals';

    protected $fillable = [
        'user_id',
        'sbp',
        'dbp',
        'hr',
        'rr',
        'spo2',
        'temp',
    ];

    protected $casts = [
        'sbp' => 'float',
        'dbp' => 'float',
        'hr' => 'float',
        'rr' => 'float',
        'spo2' => 'float',
        'temp' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
