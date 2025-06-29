@component('mail::message')
# New Contact Us Message Received.

Details are shown below.

**From:** {{ $email->sender_name }}.

**Email:** {{ $email->sender_email }}.

**Subject:** {{ $email->subject }}.

**Message:** {{ $email->message}}.



You can reply to this message by clicking the button.

@component('mail::button', ['url' => route('emails.email')])
Emails
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
