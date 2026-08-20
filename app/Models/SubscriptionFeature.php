<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionFeature extends Model
{
    use HasFactory;

    protected $table = 'subscription_features';

    protected $fillable = [
        'slug',
        'title',
        'description',
    ];
}
