<x-mail::message>
# Novo kontaktno sporočilo

Prejeli ste novo sporočilo prek kontaktnega obrazca na {{ config('app.name') }}.

**Ime:** {{ $contactMessage->name }}
**E-pošta:** {{ $contactMessage->email }}
**Zadeva:** {{ $contactMessage->subject }}

**Sporočilo:**

{{ $contactMessage->message }}

<x-mail::button :url="url('/admin')">
Odpri administracijo
</x-mail::button>

Lep pozdrav,<br>
{{ config('app.name') }}
</x-mail::message>
