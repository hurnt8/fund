<x-mail::message>
# Nouveau message

<p style="font-size: 14px !important;">
   Vous avez un nouveau message depuis Credixa
   <br>
   <br>
   Informations de l'expéditeur : <br>
      <strong>Nom</strong> : {{$data['name']}} <br>
      <strong>Email</strong> : {{$data['email']}} <br>
      <strong>Objet</strong> : {{$data['subject']}}
</p>

<strong>Message</strong>
<p style="font-size: 12px !important">
    {{$data['message']}}
</p> 
</x-mail::message>
