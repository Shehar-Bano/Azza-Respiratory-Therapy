<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'login_method',
        'fcm_token',
        'location',
        'city',
        'latitude',
        'longitude',
        'profile_image',
        'push_enabled',
        'notification_channels',
        'email_verified_at',
        'age',
        'gender',
        'height',
        'weight',
        'bmi',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'profile_image_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'age' => 'integer',
            'height' => 'float',
            'weight' => 'float',
            'bmi' => 'float',
            'latitude' => 'float',
            'longitude' => 'float',
            'push_enabled' => 'boolean',
            'notification_channels' => 'array',
        ];
    }

    /**
     * Exact 4 notification channel names supported by the application.
     *
     * @return array<string, bool>
     */
    public static function defaultNotificationChannels(): array
    {
        return [
            'General Notification' => true,
            'Reminder' => true,
            'Update' => true,
            'Security Alert' => true,
        ];
    }

    /**
     * Get merged notification preferences for the user.
     *
     * @return array{push_enabled: bool, channels: array<string, bool>}
     */
    public function getNotificationPreferences(): array
    {
        $defaults = self::defaultNotificationChannels();
        $saved = is_array($this->notification_channels) ? $this->notification_channels : [];

        $channels = [];
        foreach ($defaults as $name => $defaultVal) {
            $channels[$name] = array_key_exists($name, $saved) ? (bool) $saved[$name] : $defaultVal;
        }

        return [
            'push_enabled' => $this->push_enabled ?? true,
            'channels' => $channels,
        ];
    }

    /**
     * Determine whether external FCM push is enabled for a given notification type.
     */
    public function isPushAllowedForType(string $type): bool
    {
        $prefs = $this->getNotificationPreferences();
        if (!$prefs['push_enabled']) {
            return false;
        }

        $typeMap = [
            'general_notification' => 'General Notification',
            'general' => 'General Notification',
            'reminder' => 'Reminder',
            'update' => 'Update',
            'security_alert' => 'Security Alert',
            'alert' => 'Security Alert',
        ];

        $normalized = strtolower(trim($type));
        $channelName = $typeMap[$normalized] ?? $type;

        return $prefs['channels'][$channelName] ?? true;
    }

    /**
     * Get full URL for profile_image if set.
     */
    public function getProfileImageUrlAttribute(): ?string
    {
        if (empty($this->profile_image)) {
            return null;
        }
        if (str_starts_with($this->profile_image, 'http://') || str_starts_with($this->profile_image, 'https://')) {
            return $this->profile_image;
        }

        return asset($this->profile_image);
    }

    /**
     * Get all subscription transactions for user.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(SubscriptionTransaction::class, 'user_id');
    }

    /**
     * Get latest active subscription for user.
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(SubscriptionTransaction::class, 'user_id')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->latestOfMany();
    }

    /**
     * Get all vitals records for user.
     */
    public function vitals(): HasMany
    {
        return $this->hasMany(UserVital::class, 'user_id');
    }

    /**
     * Get latest vital record for user.
     */
    public function latestVital(): HasOne
    {
        return $this->hasOne(UserVital::class, 'user_id')->latestOfMany();
    }

    /**
     * Get all labs records for user.
     */
    public function labs(): HasMany
    {
        return $this->hasMany(UserLab::class, 'user_id');
    }

    /**
     * Get latest lab record for user.
     */
    public function latestLab(): HasOne
    {
        return $this->hasOne(UserLab::class, 'user_id')->latestOfMany();
    }

    /**
     * Route notifications for the FCM channel.
     */
    public function routeNotificationForFcm(): ?string
    {
        return $this->fcm_token;
    }
}

