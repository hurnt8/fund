<x-mail::message>
# Demande de prêt

<p style="font-size: 12px !important;">
    <h1>Informations du client :</h1>
    <p><strong>Nom et prénoms :</strong> {{ $data['name'] }}</p>
    <p><strong>Email :</strong> {{ $data['email'] }}</p>
    <p><strong>Numéro de téléphone :</strong> {{ $data['phone'] }}</p>
    <p><strong>Adresse :</strong> {{ $data['address'] }}</p>
    <p><strong>Montant du prêt souhaité :</strong> {{ $data['amount'] }} €</p>
    <p><strong>Durée du prêt :</strong> {{ $data['darly'] }} mois</p>
    <p><strong>type de prêt :</strong> {{ $data['subject'] }}</p>
</p>

<strong>Message</strong>
<p style="font-size: 12px !important">
    {{$data['objet']}}
</p> 

</x-mail::message>
