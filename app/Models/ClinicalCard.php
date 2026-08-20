<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ClinicalCard extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clinical_cards';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'document',
    ];

    /**
     * Get the images for the clinical card.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ClinicalCardImage::class, 'clinical_card_id');
    }
}
