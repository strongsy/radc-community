@component('mail::message')
# Thank you for contacting us.

<x-markdown>
    {!! $email->message !!}
</x-markdown>

## To respond to this message, please click the button below.

@component('mail::button', ['url' => config('app.url') . '/contact'])
    Contact Us
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
