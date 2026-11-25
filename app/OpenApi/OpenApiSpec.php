<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0+',
    title: 'Azza Respiratory Therapy API',
    description: 'Backend API Requirements & Specification Guide for Azza Respiratory Therapy (v1.0.0+)'
)]
#[OA\Server(url: '/', description: 'Current Host')]
#[OA\Server(url: 'https://lightseagreen-fox-847232.hostingersite.com/public', description: 'Production Server')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum',
    description: 'Enter your Bearer token (e.g. 12|abcxyz...)'
)]
class OpenApiSpec
{
    // =========================================================================
    // 2. User Profile & Location APIs
    // =========================================================================

    #[OA\Post(
        path: '/api/user/update-profile',
        summary: 'Update User Profile & Location',
        description: 'Allows users to update their Full Name, Email Address, Phone Number, Profile Image, and saved Google Maps Location with geographic coordinates.',
        tags: ['User Profile & Location'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['name', 'email'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'Dr. Sarah Jenkins'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'sarah.jenkins@hospital.org'),
                        new OA\Property(property: 'phone', type: 'string', example: '+966539301053'),
                        new OA\Property(property: 'location', type: 'string', example: 'King Fahd Rd, Al Olaya, Riyadh 12211, Saudi Arabia'),
                        new OA\Property(property: 'city', type: 'string', example: 'Riyadh'),
                        new OA\Property(property: 'latitude', type: 'number', format: 'double', example: 24.713552),
                        new OA\Property(property: 'longitude', type: 'number', format: 'double', example: 46.675296),
                        new OA\Property(property: 'profile_image', type: 'string', format: 'binary', description: 'Avatar image (image/jpeg, image/png, max 5MB)'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Profile updated successfully.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function updateProfile(): void {}

    #[OA\Get(
        path: '/api/user/profile',
        summary: 'Get User Profile',
        description: 'Retrieves the authenticated user current profile data including location, city, coordinates, avatar, and subscription details.',
        tags: ['User Profile & Location'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'User profile retrieved.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function getProfile(): void {}

    #[OA\Delete(
        path: '/api/user/delete-account',
        summary: 'Delete User Account',
        description: 'Permanently deletes the authenticated user account and all associated personal data (Apple App Store & Google Play compliance).',
        tags: ['User Profile & Location'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'reason', type: 'string', example: 'No longer needed'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'User account and all associated personal data have been permanently deleted.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function deleteAccount(): void {}

    // =========================================================================
    // 3. Support Contact & FAQs APIs
    // =========================================================================

    #[OA\Get(
        path: '/api/support-contact',
        summary: 'Get Support Contact Details',
        description: 'Returns official contact details displayed on the Support Page.',
        tags: ['Support & FAQs'],
        responses: [
            new OA\Response(response: 200, description: 'Support contact details retrieved.'),
        ]
    )]
    public function getSupportContact(): void {}

    #[OA\Get(
        path: '/api/faqs',
        summary: 'Get Questions and Answers / FAQs',
        description: 'Returns categorized clinical and account FAQs with optional category and keyword search filters.',
        tags: ['Support & FAQs'],
        parameters: [
            new OA\Parameter(name: 'category', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: 'Clinical Calculators'),
            new OA\Parameter(name: 'search', in: 'query', required: false, schema: new OA\Schema(type: 'string'), example: 'MAP'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'FAQs retrieved successfully.'),
        ]
    )]
    public function getFaqs(): void {}

    // =========================================================================
    // 4. User Feedback & Suggestion Submission
    // =========================================================================

    #[OA\Post(
        path: '/api/feedback/submit',
        summary: 'Submit User Feedback',
        description: 'Allows authenticated users to send clinical inquiries or feedback directly to the support team.',
        tags: ['User Feedback'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['message'],
                properties: [
                    new OA\Property(property: 'message', type: 'string', example: 'Please add pediatric thresholds for the ROX Index calculator.'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Feedback submitted successfully.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function submitFeedback(): void {}

    // =========================================================================
    // 5. Notification Channel Preferences & Notifications APIs
    // =========================================================================

    #[OA\Get(
        path: '/api/user/notification-preferences',
        summary: 'Get Notification Preferences',
        description: 'Retrieves push_enabled master switch and channel toggles (General Notification, Reminder, Update, Security Alert).',
        tags: ['Notification Preferences'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Notification preferences retrieved.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function getNotificationPreferences(): void {}

    #[OA\Post(
        path: '/api/user/notification-preferences',
        summary: 'Update Notification Preferences',
        description: 'Updates push_enabled master switch and the 4 notification channel toggles.',
        tags: ['Notification Preferences'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['push_enabled', 'channels'],
                properties: [
                    new OA\Property(property: 'push_enabled', type: 'boolean', example: true),
                    new OA\Property(
                        property: 'channels',
                        type: 'object',
                        properties: [
                            new OA\Property(property: 'General Notification', type: 'boolean', example: true),
                            new OA\Property(property: 'Reminder', type: 'boolean', example: true),
                            new OA\Property(property: 'Update', type: 'boolean', example: true),
                            new OA\Property(property: 'Security Alert', type: 'boolean', example: true),
                        ]
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Notification preferences updated successfully.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function updateNotificationPreferences(): void {}

    #[OA\Get(
        path: '/api/notifications',
        summary: 'Get In-App Notifications',
        tags: ['Notification Preferences'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'type', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Notifications retrieved successfully.'),
        ]
    )]
    public function getNotifications(): void {}

    #[OA\Post(
        path: '/api/user/fcm-token',
        summary: 'Update FCM Token',
        tags: ['Notification Preferences'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fcm_token'],
                properties: [
                    new OA\Property(property: 'fcm_token', type: 'string', example: 'fcm_device_token_here'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'FCM token updated successfully.'),
        ]
    )]
    public function updateFcmToken(): void {}

    // =========================================================================
    // 6. Authentication & OTP Verification Endpoints
    // =========================================================================

    #[OA\Post(
        path: '/api/login',
        summary: 'User Login',
        tags: ['Authentication & OTP'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'sarah.jenkins@hospital.org'),
                    new OA\Property(property: 'password', type: 'string', example: 'SecurePassword#2026'),
                    new OA\Property(property: 'fcm_token', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Login successful.'),
            new OA\Response(response: 401, description: 'Invalid credentials.'),
        ]
    )]
    public function login(): void {}

    #[OA\Post(
        path: '/api/register',
        summary: 'User Registration',
        tags: ['Authentication & OTP'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Dr. Sarah Jenkins'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'sarah.jenkins@hospital.org'),
                    new OA\Property(property: 'password', type: 'string', example: 'SecurePassword#2026'),
                    new OA\Property(property: 'password_confirmation', type: 'string', example: 'SecurePassword#2026'),
                    new OA\Property(property: 'phone', type: 'string', example: '+966539301053'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User registered successfully. An OTP has been sent to your email.'),
            new OA\Response(response: 422, description: 'Validation error.'),
        ]
    )]
    public function register(): void {}

    #[OA\Post(
        path: '/api/verify-email-otp',
        summary: 'Verify Email OTP',
        tags: ['Authentication & OTP'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'otp'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'sarah.jenkins@hospital.org'),
                    new OA\Property(property: 'otp', type: 'string', example: '482910'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Email verified successfully.'),
            new OA\Response(response: 400, description: 'Invalid or expired OTP.'),
        ]
    )]
    public function verifyEmailOtp(): void {}

    #[OA\Post(
        path: '/api/forgot-password',
        summary: 'Forgot Password',
        tags: ['Authentication & OTP'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'sarah.jenkins@hospital.org'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Password reset code sent to your email.'),
            new OA\Response(response: 404, description: 'User not found.'),
        ]
    )]
    public function forgotPassword(): void {}

    #[OA\Post(
        path: '/api/verify-reset-otp',
        summary: 'Verify Password Reset OTP',
        tags: ['Authentication & OTP'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'otp'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'sarah.jenkins@hospital.org'),
                    new OA\Property(property: 'otp', type: 'string', example: '592184'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'OTP verified successfully.'),
            new OA\Response(response: 400, description: 'Invalid or expired OTP.'),
        ]
    )]
    public function verifyResetOtp(): void {}

    #[OA\Post(
        path: '/api/reset-password',
        summary: 'Reset Password',
        tags: ['Authentication & OTP'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['token', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'token', type: 'string', example: 'reset_token_abc123xyz'),
                    new OA\Property(property: 'password', type: 'string', example: 'NewSecurePassword#2026'),
                    new OA\Property(property: 'password_confirmation', type: 'string', example: 'NewSecurePassword#2026'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Your password has been reset successfully.'),
            new OA\Response(response: 401, description: 'Invalid or expired reset token.'),
        ]
    )]
    public function resetPassword(): void {}

    #[OA\Post(
        path: '/api/logout',
        summary: 'Logout',
        tags: ['Authentication & OTP'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logout successful.'),
        ]
    )]
    public function logout(): void {}

    // =========================================================================
    // 7. Clinical Content & Catalog APIs
    // =========================================================================

    #[OA\Get(
        path: '/api/category/get',
        summary: 'RT Classes & Categories',
        tags: ['Clinical Content & Catalog'],
        responses: [
            new OA\Response(response: 200, description: 'Categories fetched successfully.'),
        ]
    )]
    public function getCategories(): void {}

    #[OA\Get(
        path: '/api/category/artical/get',
        summary: 'Categorized Articles',
        tags: ['Clinical Content & Catalog'],
        parameters: [
            new OA\Parameter(name: 'category_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Category articles fetched successfully.'),
        ]
    )]
    public function getCategoryArticles(): void {}

    #[OA\Get(
        path: '/api/article/get',
        summary: 'Clinical Articles Catalog',
        tags: ['Clinical Content & Catalog'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Articles fetched successfully.'),
        ]
    )]
    public function getArticles(): void {}

    #[OA\Get(
        path: '/api/card/get',
        summary: 'Clinical Flashcards Catalog',
        tags: ['Clinical Content & Catalog'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Cards fetched successfully.'),
        ]
    )]
    public function getCards(): void {}

    // =========================================================================
    // 8. Subscription & Payment Verification APIs
    // =========================================================================

    #[OA\Get(
        path: '/api/user/subscription-status',
        summary: 'Check User Subscription Status',
        tags: ['Subscriptions & Payments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'user_id', in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 42),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Subscription status retrieved.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function getSubscriptionStatus(): void {}

    #[OA\Post(
        path: '/api/verify-payment',
        summary: 'Verify Payment (Paystack)',
        tags: ['Subscriptions & Payments'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['reference', 'plan_id'],
                properties: [
                    new OA\Property(property: 'reference', type: 'string', example: 'pstk_ref_982348234'),
                    new OA\Property(property: 'plan_id', type: 'integer', example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Payment verified and subscription activated successfully.'),
            new OA\Response(response: 400, description: 'Payment verification failed.'),
            new OA\Response(response: 401, description: 'Unauthenticated.'),
        ]
    )]
    public function verifyPayment(): void {}

    #[OA\Get(
        path: '/api/subscription-plans/get',
        summary: 'Get Subscription Plans',
        tags: ['Subscriptions & Payments'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Plans retrieved successfully.'),
        ]
    )]
    public function getPlans(): void {}

    #[OA\Get(
        path: '/api/user/payment-history',
        summary: 'Get User Payment History',
        tags: ['Subscriptions & Payments'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Payment history retrieved.'),
        ]
    )]
    public function getPaymentHistory(): void {}

    // =========================================================================
    // Vitals & Labs
    // =========================================================================

    #[OA\Get(
        path: '/api/vitals-labs',
        summary: 'Get User Vitals & Labs',
        tags: ['Vitals & Labs'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Health data retrieved.'),
        ]
    )]
    public function getVitalsLabs(): void {}

    #[OA\Post(
        path: '/api/vitals-labs',
        summary: 'Save User Vitals & Labs',
        tags: ['Vitals & Labs'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Health data saved.'),
        ]
    )]
    public function saveVitalsLabs(): void {}
}
