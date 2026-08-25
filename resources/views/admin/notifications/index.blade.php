@extends('layouts.admin')

@section('title', 'Push Notifications')

@section('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title-group h1 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #ffffff;
        letter-spacing: -0.02em;
    }

    .page-title-group p {
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }

    .notification-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 1.5rem;
    }

    @media (max-width: 1024px) {
        .notification-grid {
            grid-template-columns: 1fr;
        }
    }

    .card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--card-border);
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #ffffff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .card-title svg {
        color: var(--primary);
        width: 20px;
        height: 20px;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        font-size: 0.825rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.4rem;
    }

    .form-control {
        width: 100%;
        background: #0d1322;
        border: 1px solid var(--card-border);
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        color: var(--text-primary);
        font-size: 0.875rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    .select-all-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #0d1322;
        padding: 0.65rem 0.85rem;
        border-radius: 8px;
        border: 1px solid var(--card-border);
        margin-bottom: 0.65rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .checkbox-label input[type="checkbox"] {
        width: 17px;
        height: 17px;
        accent-color: var(--primary);
        cursor: pointer;
    }

    .badge-fcm {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .badge-fcm-active {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .badge-fcm-inactive {
        background: rgba(107, 114, 128, 0.15);
        color: #9ca3af;
        border: 1px solid rgba(107, 114, 128, 0.3);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary) 0%, #4f46e5 100%);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        box-shadow: 0 4px 14px var(--primary-glow);
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px var(--primary-glow);
    }

    /* Custom Select2 Dark Styling */
    .select2-container--default .select2-selection--multiple {
        background-color: #0d1322 !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 8px !important;
        min-height: 46px !important;
        padding: 4px 8px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        color: #f8fafc !important;
        border-radius: 6px !important;
        padding: 4px 10px 4px 8px !important;
        font-size: 0.825rem !important;
        font-weight: 500 !important;
        margin-top: 4px !important;
        margin-bottom: 4px !important;
        margin-right: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }

    /* Prominent Red Remove (X) Button */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #f87171 !important;
        background: rgba(239, 68, 68, 0.18) !important;
        border: none !important;
        border-radius: 50% !important;
        width: 18px !important;
        height: 18px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        line-height: 1 !important;
        margin-right: 2px !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background: #ef4444 !important;
        color: #ffffff !important;
    }

    .select2-dropdown {
        background-color: #111827 !important;
        border: 1px solid var(--card-border) !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;
    }

    .select2-container--default .select2-search--inline .select2-search__field {
        color: var(--text-primary) !important;
        font-family: inherit !important;
        margin-top: 5px !important;
        caret-color: var(--primary) !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }

    .select2-container--default .select2-results__option {
        padding: 8px 12px !important;
        color: var(--text-primary) !important;
        font-size: 0.85rem !important;
    }

    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: var(--primary) !important;
        color: #ffffff !important;
    }

    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #1f2937 !important;
        color: var(--text-muted) !important;
    }

    /* History Table Styling */
    .table-container {
        overflow-x: auto;
        border-radius: 8px;
        border: 1px solid var(--card-border);
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.85rem;
    }

    .data-table th {
        background: #0d1322;
        color: var(--text-secondary);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.725rem;
        letter-spacing: 0.05em;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--card-border);
    }

    .data-table td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--card-border);
        color: var(--text-primary);
        vertical-align: middle;
    }

    .data-table tr:last-child td {
        border-bottom: none;
    }

    .data-table tr:hover td {
        background: rgba(255, 255, 255, 0.02);
    }

    .read-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
    }

    .read-badge-yes {
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
    }

    .read-badge-no {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
    }

    .pagination-wrapper {
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-title-group">
        <h1>Send Push Notifications</h1>
        <p>Broadcast FCM push notifications and in-app database alerts to registered users.</p>
    </div>
</div>

<div class="notification-grid">
    <!-- Form Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span>New Notification Form</span>
            </div>
        </div>

        <form action="{{ route('admin.notifications.send') }}" method="POST" id="notificationForm">
            @csrf

            <!-- Target Users Selection -->
            <div class="form-group">
                <label class="form-label">Select Recipient Users</label>
                
                <div class="select-all-container">
                    <label class="checkbox-label">
                        <input type="checkbox" name="select_all" id="selectAllCheckbox" value="1">
                        <span>Select All Users ({{ $users->count() }} total users)</span>
                    </label>
                    <span class="badge-fcm badge-fcm-active">
                        {{ $users->whereNotNull('fcm_token')->count() }} FCM Ready
                    </span>
                </div>

                <div id="userSelectContainer">
                    <select name="user_ids[]" id="userSelect" class="select2-select" multiple="multiple" style="width: 100%;">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }}) {{ !empty($user->fcm_token) ? '✓ [FCM Token]' : '✗ [No Token]' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.35rem; display: block;">
                    You can select multiple individual users or check "Select All Users" above.
                </small>
            </div>

            <!-- Notification Type -->
            <div class="form-group">
                <label class="form-label" for="type">Notification Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="general_notification">General Notification</option>
                    <option value="admin_broadcast">Admin Announcement</option>
                    <option value="alert">System Alert</option>
                    <option value="promotion">Promotional / Offer</option>
                </select>
            </div>

            <!-- Title -->
            <div class="form-group">
                <label class="form-label" for="title">Notification Title *</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="e.g. Important Update Regarding Respiratory Therapy" required value="{{ old('title') }}">
            </div>

            <!-- Description / Message -->
            <div class="form-group">
                <label class="form-label" for="message">Notification Message Body *</label>
                <textarea name="message" id="message" class="form-control" rows="4" placeholder="Enter notification description message here..." required>{{ old('message') }}</textarea>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit" id="submitBtn">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span>Send Push Notification</span>
            </button>
        </form>
    </div>

    <!-- Sent History Card -->
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Recent Notifications History</span>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Recipient User</th>
                        <th>Title & Message</th>
                        <th>Type</th>
                        <th>Read</th>
                        <th>Sent Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr>
                            <td>
                                <div>
                                    <strong style="color:#ffffff;">{{ $notification->user->name ?? 'User #'.$notification->user_id }}</strong>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $notification->user->email ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong style="color: var(--primary);">{{ $notification->title }}</strong>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.2rem; line-height: 1.3;">
                                        {{ Str::limit($notification->description, 70) }}
                                    </p>
                                </div>
                            </td>
                            <td>
                                <span class="badge-fcm badge-fcm-inactive">{{ $notification->type }}</span>
                            </td>
                            <td>
                                @if($notification->read_at)
                                    <span class="read-badge read-badge-yes">Read</span>
                                @else
                                    <span class="read-badge read-badge-no">Unread</span>
                                @endif
                            </td>
                            <td style="white-space: nowrap; font-size: 0.775rem; color: var(--text-muted);">
                                {{ $notification->created_at ? $notification->created_at->format('M d, Y H:i') : 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No notifications sent yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications->hasPages())
            <div class="pagination-wrapper">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<!-- jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 on User Dropdown
        const $userSelect = $('#userSelect').select2({
            placeholder: "Search and select user(s)...",
            allowClear: true,
            width: '100%'
        });

        let isProgrammaticChange = false;

        // Select All Checkbox Handler
        $('#selectAllCheckbox').on('change', function() {
            isProgrammaticChange = true;
            if ($(this).is(':checked')) {
                $('#userSelect option').prop('selected', true);
            } else {
                $('#userSelect option').prop('selected', false);
            }
            $userSelect.trigger('change');
            isProgrammaticChange = false;
        });

        // User Selection Change Handler (Two-Way Sync & Unselect support)
        $userSelect.on('change', function() {
            if (isProgrammaticChange) return;

            const totalOptions = $('#userSelect option').length;
            const selectedOptions = $(this).val() ? $(this).val().length : 0;

            if (selectedOptions === totalOptions && totalOptions > 0) {
                $('#selectAllCheckbox').prop('checked', true);
            } else {
                $('#selectAllCheckbox').prop('checked', false);
            }
        });

        // Form Submit Confirmation
        $('#notificationForm').on('submit', function(e) {
            const isSelectAll = $('#selectAllCheckbox').is(':checked');
            const selectedCount = $('#userSelect').val() ? $('#userSelect').val().length : 0;
            const title = $('#title').val();

            if (!isSelectAll && selectedCount === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'No User Selected',
                    text: 'Please select at least one user or check "Select All Users".',
                    background: '#161e2e',
                    color: '#ffffff'
                });
                return false;
            }

            const targetText = isSelectAll ? 'ALL registered users' : `${selectedCount} selected user(s)`;
            
            e.preventDefault();
            Swal.fire({
                title: 'Send Push Notification?',
                html: `Are you sure you want to send notification <strong>"${title}"</strong> to <strong>${targetText}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Send Now',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'swal2-confirm',
                    cancelButton: 'swal2-cancel'
                },
                buttonsStyling: false,
                background: '#161e2e',
                color: '#ffffff'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $('#submitBtn').prop('disabled', true).html('Sending notifications...');
                    document.getElementById('notificationForm').submit();
                }
            });
        });
    });
</script>
@endsection
