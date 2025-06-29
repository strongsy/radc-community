@component('mail::message')
# Good news. Your account has been authorised.

Dear {{ $user->first_name }},

You can now login to your account using the credentials that you provided when you registered.

When you first log in, you will be asked to verify your email address.

If you have any questions, please contact us using the button below.

@component('mail::button', ['url' => route('contact')])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
