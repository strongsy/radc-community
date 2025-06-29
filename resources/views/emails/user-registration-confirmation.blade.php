@component('mail::message')
# Thank you for registering with us.

Your request for access to the radc veterans community has been received.

Admin will review your request and if approved, you will receive a confirmation email.

If you have been waiting for more than 72 hours, please contact us by clicking the button below.

@component('mail::button', ['url' => route('contact')])
Contact Us
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
