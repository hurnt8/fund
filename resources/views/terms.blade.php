@extends('layouts.app')
@section('title', __('menu.terms'))

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="page-hero">
    <div class="container">
        <div class="page-hero__content">
            <h1 class="page-hero__title">@lang('menu.terms')</h1>
            <ul class="page-hero__breadcrumb">
                <li><a href="{{ route('home', ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                <li class="sep"><i class="fas fa-chevron-right"></i></li>
                <li>@lang('menu.terms')</li>
            </ul>
        </div>
    </div>
</div>

<section class="py-24 bg-white">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="legal-content">
                    <h2>Conditions générales d'utilisation</h2>
                    <p>En accédant et en utilisant le site web de Credixa, vous acceptez d'être lié par les présentes conditions générales d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser ce site.</p>

                    <h2>Utilisation du site</h2>
                    <p>Ce site est destiné à des fins d'information et de demande de prêts. Toute utilisation frauduleuse, illégale ou nuisible est strictement interdite. Credixa se réserve le droit de refuser l'accès au site à tout moment et sans préavis.</p>

                    <h2>Services proposés</h2>
                    <p>Credixa propose des services de prêt personnalisés pour les particuliers et les entreprises. Les conditions spécifiques de chaque prêt (montant, durée, taux d'intérêt) sont définies lors du processus de demande et peuvent varier selon votre profil financier.</p>

                    <h2>Confidentialité des données</h2>
                    <p>Les informations personnelles que vous fournissez lors de votre demande de prêt sont traitées conformément à notre <a href="{{ route('privacy', ['locale' => $locale]) }}">politique de confidentialité</a>. Nous nous engageons à protéger vos données et à ne pas les partager avec des tiers sans votre consentement.</p>

                    <h2>Limitation de responsabilité</h2>
                    <p>Credixa ne pourra être tenu responsable des dommages directs ou indirects résultant de l'utilisation ou de l'impossibilité d'utiliser ce site. Les informations fournies sur ce site ont un caractère indicatif et ne constituent pas un engagement contractuel.</p>

                    <h2>Propriété intellectuelle</h2>
                    <p>L'ensemble du contenu de ce site (textes, images, logos, graphiques) est la propriété exclusive de Credixa et est protégé par les lois applicables en matière de droits d'auteur. Toute reproduction ou utilisation sans autorisation préalable est interdite.</p>

                    <h2>Modification des conditions</h2>
                    <p>Credixa se réserve le droit de modifier les présentes conditions à tout moment. Les modifications prennent effet dès leur publication sur le site. Il vous appartient de consulter régulièrement cette page.</p>

                    <h2>Contact</h2>
                    <p>Pour toute question concernant ces conditions d'utilisation, veuillez nous contacter à <a href="mailto:contact@credixa.eu">contact@credixa.eu</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
