@component('mail::message')
    Hello {{$user['firstName']}}

    {{$user['firstName']}}! Have you forgotten your password?. Click on reset button

  @component('mail::button', ['url' =>url('/password/reset/'.$user['token'])])
    Reset
  @endcomponent

  Thanks,<br>
  {{ config('app.name') }}
@endcomponent
