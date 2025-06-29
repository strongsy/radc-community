@component('mail::message')
# Your message has been sent.

Thank you for contacting us. We will get back to you shortly.

**Your message:** {{ $email->message }}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
