@php
    use App\Models\Setting;
@endphp

<aside class="sidebar" id="adminSidebar"> {{-- Added ID for JS --}}
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard') }}" class="logo"> {{-- Make the whole logo a link --}}
            <div class="logo-icon">
                {{-- Use site favicon or a default icon if no specific admin logo setting --}}
                @if (Setting::get('admin_logo_icon_class'))
                    <i class="{{ Setting::get('admin_logo_icon_class', 'fas fa-shield-alt') }}"></i>
                @elseif(Setting::get('favicon_image'))
                    <img src="{{ asset(Setting::get('favicon_image')) }}" alt="Favicon"
                        style="width: 24px; height: 24px;">
                @else
                    <i class="fas fa-shield-alt"></i>
                @endif
            </div>
            <span>{{ Setting::get('site_name_short', config('app.name')) }}</span>
        </a>
    </div>

    <nav class="nav-menu">
        {{-- Dashboard Section --}}
        <div class="nav-section">
            {{-- <div class="nav-section-title">Main Menu</div> --}}
            <ul> {{-- Wrap items in UL for proper list semantics --}}
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt nav-icon"></i>
                        <span>Overview</span>
                    </a>
                </li>
                {{-- Example: Analytics link --}}
                {{-- @can('view-analytics')
                <li>
                    <a href="{{ route('admin.analytics') }}" class="nav-item {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar nav-icon"></i>
                        <span>Analytics</span>
                    </a>
                </li>
                @endcan --}}
            </ul>
        </div>

        {{-- User Access Management Section --}}
        @canany(['manage-users', 'view-users', 'manage-roles', 'view-roles', 'manage-permissions', 'view-permissions'])
            <div class="nav-section">
                <div class="nav-section-title">Access Control</div>
                <ul>
                    <li class="nav-dropdown">
                        <a href="#"
                            class="nav-item dropdown-toggle {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                            <i class="fas fa-users-cog nav-icon"></i>
                            <span>Users & Roles</span>
                            <i class="fas fa-chevron-down dropdown-icon"></i> {{-- Ensure JS rotates this --}}
                        </a>
                        <ul
                            class="dropdown-menu {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'dropdown-open' : '' }}">
                            @canany(['manage-users', 'view-users'])
                                <li><a href="{{ route('admin.users.index') }}"
                                        class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                        <i class="fas fa-users nav-icon"></i><span>Users</span></a>
                                </li>
                            @endcanany
                            @canany(['manage-roles', 'view-roles'])
                                <li><a href="{{ route('admin.roles.index') }}"
                                        class="dropdown-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                        <i class="fas fa-user-tag nav-icon"></i><span>Roles</span></a>
                                </li>
                            @endcanany
                            @canany(['manage-permissions', 'view-permissions'])
                                <li><a href="{{ route('admin.permissions.index') }}"
                                        class="dropdown-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                                        <i class="fas fa-key nav-icon"></i><span>Permissions</span></a>
                                </li>
                            @endcanany
                        </ul>
                    </li>
                </ul>
            </div>
        @endcanany




        {{-- Location Management Section Placeholder --}}
        @canany(['manage-divisions', 'manage-districts', 'manage-upazilas'])
            <div class="nav-section">
                <div class="nav-section-title">Location Management</div>
                <ul>
                    <li class="nav-dropdown">
                        <a href="#"
                            class="nav-item dropdown-toggle {{ request()->routeIs('admin.divisions.*') || request()->routeIs('admin.districts.*') || request()->routeIs('admin.upazilas.*') ? 'active' : '' }}">
                            <i class="fas fa-map-marked-alt nav-icon"></i>
                            <span>Locations</span>
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </a>
                        <ul
                            class="dropdown-menu {{ request()->routeIs('admin.divisions.*') || request()->routeIs('admin.districts.*') || request()->routeIs('admin.upazilas.*') ? 'dropdown-open' : '' }}">
                            @can('manage-divisions')
                                <li><a href="{{-- route('admin.divisions.index') --}}"
                                        class="dropdown-item {{ request()->routeIs('admin.divisions.*') ? 'active' : '' }}">
                                        <i class="fas fa-globe-asia nav-icon"></i><span>Divisions</span></a>
                                </li>
                            @endcan
                            @can('manage-districts')
                                <li><a href="{{-- route('admin.districts.index') --}}"
                                        class="dropdown-item {{ request()->routeIs('admin.districts.*') ? 'active' : '' }}">
                                        <i class="fas fa-map nav-icon"></i><span>Districts</span></a>
                                </li>
                            @endcan
                            @can('manage-upazilas')
                                <li><a href="{{-- route('admin.upazilas.index') --}}"
                                        class="dropdown-item {{ request()->routeIs('admin.upazilas.*') ? 'active' : '' }}">
                                        <i class="fas fa-map-pin nav-icon"></i><span>Upazilas</span></a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                </ul>
            </div>
        @endcanany


        {{-- ... other sidebar items ... --}}

        {{-- Financial Management Section --}}
        @canany(['manage-finances', 'view-all-payments', 'manage-payment-accounts', 'manage-payment-method-settings',
            'view-financial-reports', 'manage-financial-categories'])
            <div class="nav-section">
                <div class="nav-section-title">Financials</div>
                <ul>
                    <li class="nav-dropdown">
                        <a href="#"
                            class="nav-item dropdown-toggle
                {{ request()->routeIs('admin.payments.*') ||
                request()->routeIs('admin.payment-accounts.*') ||
                request()->routeIs('admin.payment-methods.*') ||
                request()->routeIs('admin.financial-ledgers.*') ||
                request()->routeIs('admin.financial-transaction-categories.*')
                    ? 'active active-parent'
                    : '' }}">
                            <i class="fas fa-coins nav-icon"></i>
                            <span>Finance & Payments</span>
                            <i class="fas fa-chevron-down dropdown-icon"></i>
                        </a>
                        <ul
                            class="dropdown-menu
                {{ request()->routeIs('admin.payments.*') ||
                request()->routeIs('admin.payment-accounts.*') ||
                request()->routeIs('admin.payment-methods.*') ||
                request()->routeIs('admin.financial-ledgers.*') ||
                request()->routeIs('admin.financial-transaction-categories.*')
                    ? 'dropdown-open'
                    : '' }}">

                            @can('view-all-payments')
                                <li><a href="{{ route('admin.payments.index') }}"
                                        class="dropdown-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                        <i class="fas fa-receipt nav-icon"></i><span>All Payments</span></a>
                                </li>
                            @endcan

                            @can('manage-payment-accounts')
                                <li><a href="{{ route('admin.payment-accounts.index') }}"
                                        class="dropdown-item {{ request()->routeIs('admin.payment-accounts.*') ? 'active' : '' }}">
                                        <i class="fas fa-university nav-icon"></i><span>Payment Accounts</span></a>
                                </li>
                            @endcan

                            @can('manage-payment-method-settings')
                                <li><a href="{{ route('admin.payment-methods.index') }}"
                                        class="dropdown-item {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                                        <i class="fas fa-credit-card nav-icon"></i><span>Payment Methods</span></a>
                                </li>
                            @endcan

                            @can('manage-financial-categories')
                                <li><a href="{{ route('admin.financial-transaction-categories.index') }}"
                                        class="dropdown-item {{ request()->routeIs('admin.financial-transaction-categories.*') ? 'active' : '' }}">
                                        <i class="fas fa-tags nav-icon"></i><span>Transaction Categories</span></a>
                                </li>
                            @endcan

                            @canany(['record-income', 'record-expense', 'view-financial-reports'])
                                <li><a href="{{ route('admin.financial-ledgers.index') }}"
                                        class="dropdown-item {{ request()->routeIs('admin.financial-ledgers.*') ? 'active' : '' }}">
                                        <i class="fas fa-book nav-icon"></i><span>Financial Ledger</span></a>
                                </li>
                            @endcan
                            {{-- Inside Financials dropdown or new Reports section in sidebar --}}
@can('view-financial-reports')
<li class="nav-dropdown">
    <a href="#" class="nav-item dropdown-toggle {{ request()->routeIs('admin.reports.*') ? 'active active-parent' : '' }}">
        <i class="fas fa-chart-line nav-icon"></i>
        <span>Financial Reports</span>
        <i class="fas fa-chevron-down dropdown-icon"></i>
    </a>
    <ul class="dropdown-menu {{ request()->routeIs('admin.reports.*') ? 'dropdown-open' : '' }}">
        <li><a href="{{ route('admin.reports.income-statement') }}" class="dropdown-item {{ request()->routeIs('admin.reports.income-statement') ? 'active' : '' }}">Income Statement</a></li>
        <li><a href="{{ route('admin.reports.balance-sheet') }}" class="dropdown-item {{ request()->routeIs('admin.reports.balance-sheet') ? 'active' : '' }}">Balance Sheet (Simple)</a></li>
        <li><a href="{{ route('admin.reports.cash-flow') }}" class="dropdown-item {{ request()->routeIs('admin.reports.cash-flow') ? 'active' : '' }}">Cash Flow (Simple)</a></li>
        <li><a href="{{ route('admin.reports.account-transactions', ['paymentAccount' => $firstPaymentAccount->id ?? 0]) }}" class="dropdown-item {{ request()->routeIs('admin.reports.account-transactions') ? 'active' : '' }}">Account Transactions</a></li> {{-- Pass a default account ID or handle no ID state --}}
        <li><a href="{{ route('admin.reports.category-summary') }}" class="dropdown-item {{ request()->routeIs('admin.reports.category-summary') ? 'active' : '' }}">Category Summary</a></li>
    </ul>
</li>
@endcan
                        </ul>
                    </li>
                </ul>
            </div>
        @endcanany

        @canany(['manage-memberships', 'view-memberships'])
            <div class="nav-section">
                <div class="nav-section-title">Membership</div>
                <ul>
                    <li>
                        <a href="{{ route('admin.memberships.index') }}"
                            class="nav-item {{ request()->routeIs('admin.memberships.*') ? 'active' : '' }}">
                            <i class="fas fa-id-card-alt nav-icon"></i>
                            <span>Manage Memberships</span>
                        </a>
                    </li>
                    @can('manage-memberships')
                        {{-- Or a more specific permission like 'manage-membership-types' --}}
                        <li>
                            <a href="{{ route('admin.membership-types.index') }}"
                                class="dropdown-item {{ request()->routeIs('admin.membership-types.*') ? 'active' : '' }}">
                                <i class="fas fa-id-badge nav-icon"></i><span>Membership Types</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </div>
        @endcanany




        {{-- ... other sidebar items ... --}}

        {{-- Settings Section --}}
        @can('manage-settings')
            <div class="nav-section">
                <div class="nav-section-title">Configuration</div>
                <ul>
                    <li>
                        <a href="{{-- route('admin.settings.index') --}}"
                            class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="fas fa-cogs nav-icon"></i>
                            <span>Site Settings</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endcan

    </nav>

    <div class="nav-user">
        <div class="user-avatar">
            @if (Auth::user()->profile_picture_path)
                <img src="{{ asset(Auth::user()->profile_picture_path) }}" alt="{{ Auth::user()->name }}"
                    style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <i class="fas fa-user"></i> {{-- Fallback icon --}}
            @endif
        </div>
        <div class="user-info">
            <div class="user-name">{{ Auth::user()->name ?? 'Guest User' }}</div>
            <div class="user-role">
                {{ Auth::user()->roles->isNotEmpty() ? Str::title(str_replace('-', ' ', Auth::user()->roles->first()->name)) : 'User' }}
            </div>
        </div>
    </div>
</aside>

{{-- The mobile toggle button is now part of the navbar.blade.php or should be placed in app.blade.php outside the sidebar itself if it's globally accessible.
    Your CSS snippet has it standalone, which means app.blade.php might be a better place if it's not part of the navbar.
    However, the navbar snippet you provided *also* has a mobile-toggle-btn. Let's assume the one in the navbar is the primary one.
--}}
