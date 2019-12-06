@component('mail::message')
# **Introduction**

The *body* of your _message_.

@component('mail::button', ['url' => 'http://music_bank.io'])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
