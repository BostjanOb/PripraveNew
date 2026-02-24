<x-mail::message>
# Odgovor na vaše sporočilo

Pozdravljeni, {{ $contactMessage->name }}.

Hvala za vaše sporočilo z zadevo **{{ $contactMessage->subject }}**.

**Naš odgovor:**

{{ $contactMessage->reply_message }}

Lep pozdrav,<br>
{{ config('app.name') }}
</x-mail::message>
