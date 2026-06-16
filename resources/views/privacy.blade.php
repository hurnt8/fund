@extends('layouts.app')
@section('title', __('menu.privacy'))

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.privacy')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.privacy')</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="legal-content">
                    <h2>Politique de confidentialité</h2>
                    <p>Credixa s'engage à protéger la confidentialité de vos informations personnelles. Cette politique décrit comment nous collectons, utilisons et protégeons vos données.</p>

                    <h2>Données collectées</h2>
                    <p>Nous collectons les informations que vous nous fournissez lors de vos demandes de prêt ou de contact, notamment :</p>
                    <ul>
                        <li>Nom et prénom</li>
                        <li>Adresse email et numéro de téléphone</li>
                        <li>Adresse postale</li>
                        <li>Informations financières (montant souhaité, durée, revenus)</li>
                        <li>Documents d'identité (le cas échéant)</li>
                    </ul>

                    <h2>Utilisation des données</h2>
                    <p>Vos données sont utilisées exclusivement pour :</p>
                    <ul>
                        <li>Traiter votre demande de prêt</li>
                        <li>Vous contacter concernant votre dossier</li>
                        <li>Améliorer nos services</li>
                        <li>Respecter nos obligations légales et réglementaires</li>
                    </ul>

                    <h2>Partage des données</h2>
                    <p>Nous ne vendons pas vos données personnelles à des tiers. Nous pouvons partager vos informations uniquement avec nos partenaires financiers dans le cadre du traitement de votre demande, ou lorsque la loi l'exige.</p>

                    <h2>Sécurité des données</h2>
                    <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles appropriées pour protéger vos données contre tout accès non autorisé, modification, divulgation ou destruction.</p>

                    <h2>Cookies</h2>
                    <p>Ce site utilise des cookies pour améliorer votre expérience de navigation. Vous pouvez configurer votre navigateur pour refuser les cookies, bien que cela puisse affecter certaines fonctionnalités du site.</p>

                    <h2>Vos droits (RGPD)</h2>
                    <p>Conformément au RGPD, vous disposez des droits suivants concernant vos données personnelles :</p>
                    <ul>
                        <li>Droit d'accès à vos données</li>
                        <li>Droit de rectification</li>
                        <li>Droit à l'effacement ("droit à l'oubli")</li>
                        <li>Droit à la portabilité des données</li>
                        <li>Droit d'opposition au traitement</li>
                    </ul>

                    <h2>Contact</h2>
                    <p>Pour exercer vos droits ou pour toute question relative à la protection de vos données, contactez notre délégué à la protection des données à <a href="mailto:contact@credixa.eu">contact@credixa.eu</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
