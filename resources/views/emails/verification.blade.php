@component('mail::message')
  Hello {{$user->first_name}}

  Congratulations  {{$user->first_name}}! You have successfully registered for, to continue your verification account.

  @component('mail::button', ['url' =>url('/verify-email/'.$user->email_token)])
    Verify
  @endcomponent

  Thanks,<br>
  {{ config('app.name') }}
@endcomponent
