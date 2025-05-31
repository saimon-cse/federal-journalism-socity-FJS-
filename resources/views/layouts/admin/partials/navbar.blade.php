@php
    // These variables would be passed from a View Composer or a BaseController in a real app
    // For now, we'll use placeholders or comment them out.
    // $unreadNoticesCount = Auth::user()->unreadNotifications()->count(); // Example for Laravel Notifications
    // $recentNotices = Auth::user()->notifications()->latest()->take(5)->get(); // Example
    $unreadNoticesCount = 0; // Placeholder
    $recentNotices = collect(); // Placeholder
@endphp
<nav class="navbar">
    <div class="navbar-left">
        <button class="mobile-toggle-btn" id="mobileSidebarToggle"> {{-- Ensure ID matches JS --}}
            <i class="fas fa-bars"></i>
        </button>
        <div class="navbar-breadcrumb d-none d-md-flex"> {{-- Hide on smaller screens initially --}}
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            @yield('breadcrumbs') {{-- Content pages can push breadcrumb items here --}}
        </div>
    </div>

    <div class="navbar-right">
        <div class="navbar-search d-none d-lg-block"> {{-- Hide on medium and smaller screens --}}
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Search...">
            </div>
        </div>

        <div class="navbar-items">
            <!-- Notifications Dropdown -->
            <div class="navbar-item dropdown">
                <button class="navbar-icon-btn dropdown-toggle" id="navbarNotificationsDropdownToggle"> {{-- Specific ID --}}
                    <i class="fas fa-bell"></i>
                    @if(isset($unreadNoticesCount) && $unreadNoticesCount > 0)
                    <span class="badge">{{ $unreadNoticesCount }}</span>
                    @endif
                </button>
                <div class="dropdown-content dropdown-notifications" id="navbarNotificationsDropdownMenu"> {{-- Specific ID --}}
                    <div class="dropdown-header">
                        <h3>Notifications</h3>
                        @if($recentNotices->count() > 0 && $unreadNoticesCount > 0)
                        <a href="#" class="mark-all-read" id="mark-all-notifications-read">Mark all as read</a>
                        @endif
                    </div>
                    <div class="dropdown-body">
                        @if(isset($recentNotices) && $recentNotices->count() > 0)
                            @foreach($recentNotices as $notice)
                                @php
                                    // Determine notification icon and color based on a 'type' or 'priority'
                                    // This is just an example, you'll need to adapt it to your notification structure
                                    $iconClass = 'fa-bell'; $bgColorClass = 'bg-primary';
                                    if (isset($notice->data['type'])) {
                                        switch ($notice->data['type']) {
                                            case 'new_user': $iconClass = 'fa-user-plus'; $bgColorClass = 'bg-info'; break;
                                            case 'payment_success': $iconClass = 'fa-check-circle'; $bgColorClass = 'bg-success'; break;
                                            case 'task_warning': $iconClass = 'fa-exclamation-triangle'; $bgColorClass = 'bg-warning'; break;
                                            default: $iconClass = 'fa-info-circle'; $bgColorClass = 'bg-primary'; break;
                                        }
                                    }
                                @endphp
                                <a href="{{-- route('admin.notifications.show', $notice->id) --}}"
                                   class="notification-item {{ !$notice->read_at ? 'unread' : '' }}"
                                   data-notification-id="{{ $notice->id }}">
                                    <div class="notification-icon {{ $bgColorClass }}">
                                        <i class="fas {{ $iconClass }}"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p class="notification-text">{{ $notice->data['message'] ?? 'Notification' }}</p>
                                        <p class="notification-time">{{ $notice->created_at->diffForHumans() }}</p>
                                    </div>
                                </a>
                            @endforeach
                        @else
                            <div class="p-3 text-center text-muted">
                                No new notifications.
                            </div>
                        @endif
                    </div>
                    @if(isset($recentNotices) && $recentNotices->count() > 0)
                    <div class="dropdown-footer">
                        <a href="{{-- route('admin.notifications.index') --}}">View all notifications</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Messages Dropdown (Placeholder) -->
            <div class="navbar-item dropdown">
                <button class="navbar-icon-btn dropdown-toggle" id="navbarMessagesDropdownToggle"> {{-- Specific ID --}}
                    <i class="fas fa-envelope"></i>
                    {{-- <span class="badge">2</span> --}} {{-- Dynamic count here --}}
                </button>
                <div class="dropdown-content dropdown-messages" id="navbarMessagesDropdownMenu" style="min-width: 340px;"> {{-- Specific ID --}}
                    <div class="dropdown-header">
                        <h3>Messages</h3>
                        {{-- <a href="#" class="mark-all-read">Mark all as read</a> --}}
                    </div>
                    <div class="dropdown-body">
                        {{-- Example Message Item --}}
                        {{-- <a href="#" class="message-item unread">
                            <div class="message-avatar">
                                <img src="{{ asset('backend/assets/images/default-avatar.png') }}" alt="User Avatar">
                            </div>
                            <div class="message-content">
                                <p class="message-sender">John Doe</p>
                                <p class="message-text">Hello, can you help me with...</p>
                                <p class="message-time">30 min ago</p>
                            </div>
                        </a> --}}
                         <div class="p-3 text-center text-muted">
                            No new messages.
                        </div>
                    </div>
                    {{-- <div class="dropdown-footer">
                        <a href="{{ route('admin.messages.index') }}">View all messages</a>
                    </div> --}}
                </div>
            </div>

            <!-- User Profile Dropdown -->
            <div class="navbar-item dropdown">
                <button class="navbar-profile-btn dropdown-toggle" id="navbarProfileDropdownToggle"> {{-- Specific ID --}}
                    <div class="profile-avatar">
                        @if(Auth::user()->profile_picture_path)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture_path) }}" alt="{{ Auth::user()->name }}">
                        @else
                            <i class="fas fa-user"></i> {{-- Fallback icon --}}
                        @endif
                    </div>
                    <div class="profile-info d-none d-md-block">
                        <span class="profile-name">{{ Auth::user()->name }}</span>
                    </div>
                    <i class="fas fa-chevron-down dropdown-arrow d-none d-md-inline-block"></i>
                </button>
                <div class="dropdown-content dropdown-profile" id="navbarProfileDropdownMenu"> {{-- Specific ID --}}
                    <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                    @can('manage-settings') {{-- Assuming you have a permission for settings --}}
                    <a href="{{-- route('admin.settings.index') --}}" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    @endcan
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" id="adminLogoutForm">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"> {{-- Styling as a link --}}
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
