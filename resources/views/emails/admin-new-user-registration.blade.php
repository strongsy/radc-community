@component('mail::message')
# A new user has registered on radc veterans community.

**User name:** {{ $user->name }}

**User email:** {{ $user->email }}

**User community:** {{ $user->community }}

**User membership:** {{ $user->membership }}

**User affiliation:** {{ $user->affiliation }}

You can authorize this user by clicking the button below.

@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
