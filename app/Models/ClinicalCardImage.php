<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicalCardImage extends Model
{
    use HasFactory;

    protected $table = 'clinical_card_images';

    protected $fillable = [
        'clinical_card_id',
        'image',
    ];

    /**
     * Get the clinical card that owns the image.
     */
    public function clinicalCard(): BelongsTo
    {
        return $this->belongsTo(ClinicalCard::class, 'clinical_card_id');
    }
}
