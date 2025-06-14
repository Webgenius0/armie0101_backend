@php
    $systemSetting = App\Models\SystemSetting::first();
@endphp

<div class="app-menu navbar-menu">
    {{-- Logo & Toggle Button --}}
    <div class="navbar-brand-box">
        <a href="{{ route('dashboard') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset($systemSetting->logo ?? 'frontend/logo_black.png') }}" alt="Logo"
                    style="width: 190px; height: 50px;">
            </span>
            <span class="logo-lg">
                <img src="{{ asset($systemSetting->logo ?? 'frontend/logo_black.png') }}" alt="Logo"
                    style="width: 190px; height: 50px;">
            </span>
        </a>

        <a href="{{ route('dashboard') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset($systemSetting->logo ?? 'frontend/logo_black.png') }}" alt="Logo"
                    style="width: 190px; height: 50px;">
            </span>
            <span class="logo-lg">
                <img src="{{ asset($systemSetting->logo ?? 'frontend/logo_black.png') }}" alt="Logo"
                    style="width: 190px; height: 50px;">
            </span>
        </a>

        <button type="button" class="btn btn-sm p-0 fs-3xl header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>

        <div class="vertical-menu-btn-wrapper header-item vertical-icon">
            <button type="button"
                class="btn btn-sm px-0 fs-xl vertical-menu-btn topnav-hamburger shadow hamburger-icon"
                id="topnav-hamburger-icon">
                <i class='bx bx-chevrons-right'></i>
                <i class='bx bx-chevrons-left'></i>
            </button>
        </div>
    </div>
    {{-- Logo & Toggle Button --}}

    <div id="scrollbar">
        {{-- Sidebar Start --}}
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="ri-dashboard-line"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                {{-- Users --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('admin/user*') ? 'active' : '' }}"
                        href="#sidebarUsers" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ request()->is('admin/user*') ? 'true' : 'false' }}"
                        aria-controls="sidebarUsers">
                        <i class="ri-group-line"></i>
                        <span data-key="t-users">Users</span>
                    </a>

                    <div class="collapse menu-dropdown {{ request()->is('admin/user*') ? 'show' : '' }}"
                        id="sidebarUsers">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('user.client.index') }}"
                                    class="nav-link {{ request()->routeIs('user.client.index') ? 'active' : '' }}"
                                    data-key="t-client">
                                    Client
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('user.beauty-expert.index') }}"
                                    class="nav-link {{ request()->routeIs('user.beauty-expert.index') ? 'active' : '' }}"
                                    data-key="t-beauty-expert">
                                    Beauty Expert
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{-- Service --}}
                <li class="nav-item">
                    <a href="{{ route('service.index') }}"
                        class="nav-link menu-link {{ request()->routeIs('service.*') ? 'active' : '' }}">
                        <i class="ri-tools-line"></i>
                        <span data-key="t-faq">Service</span>
                    </a>
                </li>

                {{-- Image Approval Request --}}
                <li class="nav-item">
                    <a href="{{ route('image-approval-request.index') }}"
                        class="nav-link menu-link {{ request()->routeIs('image-approval-request.*') ? 'active' : '' }}">
                        <i class="ri-image-edit-line"></i>
                        <span data-key="t-faq" style="white-space: nowrap">Image Approval Request</span>
                    </a>
                </li>

                {{-- Frequently Asked Questions --}}
                <li class="nav-item">
                    <a href="{{ route('faq.index') }}"
                        class="nav-link menu-link {{ request()->routeIs('faq.*') ? 'active' : '' }}">
                        <i class="ri-question-line"></i>
                        <span data-key="t-faq">FAQ</span>
                    </a>
                </li>

                {{-- Contacts --}}
                <li class="nav-item">
                    <a href="{{ route('contacts.index') }}"
                        class="nav-link menu-link {{ request()->routeIs('contacts.*') ? 'active' : '' }}">
                        <i class="ri-contacts-book-line"></i>
                        <span data-key="t-Contact">Contacts</span>
                    </a>
                </li>

                {{-- Testimonial --}}
                <li class="nav-item">
                    <a href="{{ route('testimonial.index') }}"
                        class="nav-link menu-link {{ request()->routeIs('testimonial.index') ? 'active' : '' }}">
                        <i class="ri-chat-quote-line"></i>
                        <span data-key="t-dashboard">Testimonial</span>
                    </a>
                </li>

                {{-- Report --}}
                <li class="nav-item">
                    <a href="{{ route('report.index') }}"
                        class="nav-link menu-link {{ request()->routeIs('report.index') ? 'active' : '' }}">
                        <i class="ri-alert-line"></i>
                        <span data-key="t-dashboard">Report</span>
                    </a>
                </li>

                {{-- Newsletter Subscriptions --}}
                <li class="nav-item">
                    <a href="{{ route('newsletter-subscription.index') }}"
                        class="nav-link menu-link {{ request()->routeIs('newsletter-subscription.index') ? 'active' : '' }}">
                        <i class="ri-mail-line"></i>
                        <span data-key="t-dashboard" style="white-space: nowrap">Newsletter Subscriptions</span>
                    </a>
                </li>

                {{-- Booking Cancellation List --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('admin/booking-cancellation*') ? 'active' : '' }}"
                        href="#bookingCancellation" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ request()->is('admin/booking-cancellation*') ? 'true' : 'false' }}"
                        aria-controls="bookingCancellation">
                        <i class="ri-close-circle-line"></i>
                        <span data-key="t-booking-cancellation" style="white-space: nowrap">Booking
                            Cancellation</span>
                    </a>

                    <div class="collapse menu-dropdown {{ request()->is('admin/booking-cancellation*') ? 'show' : '' }}"
                        id="bookingCancellation">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('before-payment.index') }}"
                                    class="nav-link {{ request()->routeIs('before-payment.index') ? 'active' : '' }}"
                                    data-key="t-before-payment">
                                    Before Payment
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('after-payment.index') }}"
                                    class="nav-link {{ request()->routeIs('after-payment.index') ? 'active' : '' }}"
                                    data-key="t-after-payment">
                                    After Payment
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <hr>
                {{-- CMS --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('admin/cms*') ? 'active' : '' }}"
                        href="#sidebarCMSPages" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ request()->is('admin/cms*') ? 'true' : 'false' }}"
                        aria-controls="sidebarCMSPages">
                        <i class="ri-pages-line"></i>
                        <span data-key="t-pages">CMS</span>
                    </a>

                    <div class="collapse menu-dropdown {{ request()->is('admin/cms*') ? 'show' : '' }}"
                        id="sidebarCMSPages">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('cms.home-page.index') }}"
                                    class="nav-link {{ request()->routeIs('cms.home-page.index') ? 'active' : '' }}"
                                    data-key="t-home-page">
                                    Home Page
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('cms.auth-page.index') }}"
                                    class="nav-link {{ request()->routeIs('cms.auth-page.index') ? 'active' : '' }}"
                                    data-key="t-auth-page">
                                    Auth Page
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('cms.testimonial.index') }}"
                                    class="nav-link {{ request()->routeIs('cms.testimonial.index') ? 'active' : '' }}"
                                    data-key="t-auth-page">
                                    Testimonial Page
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('cms.questionnaires.index') }}"
                                    class="nav-link {{ request()->routeIs('cms.questionnaires.index') ? 'active' : '' }}"
                                    data-key="t-questionnaires">
                                    Questionnaires
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('cms.join-us.index') }}"
                                    class="nav-link {{ request()->routeIs('cms.join-us.index') ? 'active' : '' }}"
                                    data-key="t-join-us">
                                    Join Us
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('cms.service-type.index') }}"
                                    class="nav-link {{ request()->routeIs('cms.service-type.index') ? 'active' : '' }}"
                                    data-key="t-service-type">
                                    Service Types
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <hr>
                {{-- Settings --}}
                <li class="nav-item">
                    <a class="nav-link menu-link {{ request()->is('admin/settings*') ? 'active' : '' }}"
                        href="#sidebarPages" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ request()->is('admin/settings*') ? 'true' : 'false' }}"
                        aria-controls="sidebarPages">
                        <i class="ri-settings-3-line"></i>
                        <span data-key="t-pages">Settings</span>
                    </a>

                    <div class="collapse menu-dropdown {{ request()->is('admin/settings*') ? 'show' : '' }}"
                        id="sidebarPages">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('profile.setting') }}"
                                    class="nav-link {{ request()->routeIs('profile.setting') ? 'active' : '' }}"
                                    data-key="t-profile-setting">
                                    Profile Settings
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('system.index') }}"
                                    class="nav-link {{ request()->routeIs('system.index') ? 'active' : '' }}"
                                    data-key="t-system-settings">
                                    System Settings
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('mail.setting') }}"
                                    class="nav-link {{ request()->routeIs('mail.setting') ? 'active' : '' }}"
                                    data-key="t-system-settings">
                                    SMTP Server
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('integration.setting') }}"
                                    class="nav-link {{ request()->routeIs('integration.setting') ? 'active' : '' }}"
                                    data-key="t-integration-settings">
                                    Integration Settings
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('social.index') }}"
                                    class="nav-link {{ request()->routeIs('social.index') ? 'active' : '' }}"
                                    data-key="t-social-media-settings">
                                    Social Media Settings
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('settings.dynamic_page.index') }}"
                                    class="nav-link {{ request()->routeIs('settings.dynamic_page.*') ? 'active' : '' }}"
                                    data-key="t-dynamic-page-settings">
                                    Dynamic Page Settings
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('terms-and-conditions.index') }}"
                                    class="nav-link {{ request()->routeIs('terms-and-conditions.index') ? 'active' : '' }}"
                                    data-key="t-terms-and-conditions">
                                    Terms & Conditions
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('privacy-policy.index') }}"
                                    class="nav-link {{ request()->routeIs('privacy-policy.index') ? 'active' : '' }}"
                                    data-key="t-terms-and-conditions">
                                    Privacy Policy
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('inclusions-cancellation.index') }}"
                                    class="nav-link {{ request()->routeIs('inclusions-cancellation.index') ? 'active' : '' }}"
                                    data-key="t-inclusions-cancellation">
                                    Inclusions & Cancellation Policy
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        {{-- Sidebar End --}}
    </div>

    <div class="sidebar-background"></div>
</div>
<div class="vertical-overlay"></div>
