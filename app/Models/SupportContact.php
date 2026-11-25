<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'support_email',
        'support_phone',
        'working_hours',
        'support_options',
    ];

    protected function casts(): array
    {
        return [
            'support_options' => 'array',
        ];
    }

    public static function defaultValues(): array
    {
        return [
            'support_email' => 'support@azzatherapy.com',
            'support_phone' => '+966 11 000 0000',
            'working_hours' => 'Sun - Thu: 9:00 AM - 6:00 PM (AST)',
            'support_options' => [
                'Direct Email Support',
                'In-App Feedback & Suggestions',
                'Frequently Asked Clinical Questions (FAQs)',
            ],
        ];
    }
}
