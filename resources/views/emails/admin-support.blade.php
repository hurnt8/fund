@component('mail::message')
# Nouveau message support

**{{ $client->name }}** vous a envoyé un message via le support Credixa.

@component('mail::panel')
{{ $message->body }}
@endcomponent

@component('mail::button', ['url' => url('/admin/support/' . $client->id), 'color' => 'primary'])
Répondre au client
@endcomponent

Reçu le {{ $message->created_at->format('d/m/Y à H:i') }}

Cordialement,<br>
**Credixa**
@endcomponent
