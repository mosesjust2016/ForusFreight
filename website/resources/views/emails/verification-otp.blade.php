@component('mail::message')
# Verify Your Email Address

Hi {{ $user->name }},

Thank you for signing up with Forus Freight! To complete your registration, please use the verification code below:

@component('mail::panel')
<div style="text-align: center; font-size: 2rem; font-weight: bold; letter-spacing: 0.5rem; color: #007f7f;">
{{ $otp }}
</div>
@endcomponent

This code will expire in **10 minutes**.

If you did not create an account, no further action is required.

Thanks,<br>
**Forus Freight Team**
@endcomponent
