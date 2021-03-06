@component('mail::message')

{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level == 'error')
# {{ __('emails.whoops') }}
@else
# {{ __('emails.hello') }}
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $_line)
{{ $_line }}
@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
    case 'success':
        $color = 'green';
        break;
    case 'error':
        $color = 'red';
        break;
    default:
        $color = 'blue';
        break;
}
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $_line)
{{ $_line }}
@endforeach

{{-- Salutation --}}
@if (!empty($salutation))
{{ $salutation }}
@else
{{ __('emails.regards') }},<br>{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
{{ __('emails.subcopy', ['actionText' => $actionText, 'actionUrl' => $actionUrl]) }}
@endcomponent
@endisset

@endcomponent
