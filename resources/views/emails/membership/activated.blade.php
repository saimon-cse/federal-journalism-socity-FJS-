@component('mail::message')
# Membership Activated!

Dear {{ $user->name }},

Congratulations! Your **{{ ucfirst(str_replace('_', ' ', $membership->membership_type)) }}** membership with {{ config('app.name') }} has been successfully activated.

**Membership Details:**
- **Type:** {{ ucfirst(str_replace('_', ' ', $membership->membership_type)) }}
- **Start Date:** {{ $membership->start_date->format('F d, Y') }}
@if($membership->end_date)
- **End Date:** {{ $membership->end_date->format('F d, Y') }}
@endif

You can now access all member benefits.

@component('mail::button', ['url' => route('user.profile.memberships')]) {{-- Adjust route as needed --}}
View Your Profile
@endcomponent

Thank you for being a valued member.

Regards,<br>
{{ config('app.name') }}
@endcomponent
