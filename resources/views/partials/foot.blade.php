@php $currentRoute = Route::currentRouteName(); @endphp
<div class="main-footer__bg"
style="background-image: url({{ asset('assets/images/shapes/footer-bg-1-1.png') }});"></div>
<!-- /.main-footer__bg -->
<div class="main-footer__top">
<div class="container">
    <div class="row gutter-y-40">
        <div class="col-xl-4 col-lg-6 wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="00ms">
            <div class="footer-widget footer-widget--about">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="footer-widget__logo">
                    <img src="{{ asset('assets/images/logo new.png') }}" width="190"
                        alt="Credixa">
                </a>
                <p class="footer-widget__about-text">{{__('home.about_text')}}</p>
                <!-- /.footer-widget__about-text -->
                <form action="{{route('subscribe.send')}}" method="POST"  class="footer-widget__newsletter ">
                    @csrf
                    <input type="text" name="email" placeholder="{{ __('home.subscribe.placeholder') }}">
                    <button type="submit">
                        <span class="sr-only">submit</span><!-- /.sr-only -->
                        <i class="icon-right-arrow"></i>
                    </button>
                </form><!-- /.footer-widget__newsletter mc-form -->
                <div class="mc-form__response mt-4">
                    @if (session('success'))
                    <div class="alert alert-info alart_style_one alert-dismissible fade show mb20" role="alert" >
                        {{ __('message.success_sbscribe') }}
                        <i class="far fa-xmark btn-close" data-bs-dismiss="alert" aria-label="Close"></i>
                    </div>
                    @endif
                    @if (session('error'))
                    <div class="alert alert-danger alart_style_three alert-dismissible fade show mb20" role="alert"  >
                        {{ __('message.error') }}
                        <i class="far fa-xmark btn-close" data-bs-dismiss="alert" aria-label="Close"></i>
                    </div>
                    @endif
                </div><!-- /.mc-form__response -->
            </div><!-- /.footer-widget -->
        </div><!-- /.col-xl-4 col-lg-6 -->
        <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
            data-wow-delay="100ms">
            <div class="footer-widget footer-widget--links footer-widget--links-one">
                <h2 class="footer-widget__title">{{__('menu.menu')}}</h2><!-- /.footer-widget__title -->
                <ul class="list-unstyled footer-widget__links">
                    <li><a href="{{ route('about', ['locale' => app()->getLocale()]) }}">@lang('menu.about')</a></li>
                    <li><a href="{{ route('services', ['locale' => app()->getLocale()]) }}">@lang('menu.services')</a></li>
                    <li><a href="{{ route('loan', ['locale' => app()->getLocale()]) }}">@lang('menu.loan')</a></li>
                    <li><a href="{{ route('contact', ['locale' => app()->getLocale()]) }}">@lang('menu.contact')</a></li>
                </ul><!-- /.list-unstyled footer-widget__links -->
            </div><!-- /.footer-widget -->
        </div><!-- /.col-xl-2 col-lg-3 col-md-3 col-sm-6 -->
        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 wow fadeInUp" data-wow-duration="1500ms"
            data-wow-delay="200ms">
            <div class="footer-widget footer-widget--links footer-widget--links-two">
                <h2 class="footer-widget__title">{{__('menu.services')}}</h2><!-- /.footer-widget__title -->
                <ul class="list-unstyled footer-widget__links">
                    <li class="{{ $currentRoute == 'services.bike' ? 'current' : '' }}" ><a href="{{ route('services.bike', ['locale' => app()->getLocale()]) }}">@lang('menu.bike')</a></li>
                    <li class="{{ $currentRoute == 'services.home' ? 'current' : '' }}" ><a href="{{ route('services.home', ['locale' => app()->getLocale()]) }}">@lang('menu.home_loan')</a></li>
                    <li class="{{ $currentRoute == 'services.study' ? 'current' : '' }}" ><a href="{{ route('services.study', ['locale' => app()->getLocale()]) }}">abroad study loan</a></li>
                    <li class="{{ $currentRoute == 'services.business' ? 'current' : '' }}" ><a href="{{ route('services.business', ['locale' => app()->getLocale()]) }}">@lang('menu.business')</a></li>
                    <li class="{{ $currentRoute == 'services.personal' ? 'current' : '' }}" ><a href="{{ route('services.personal', ['locale' => app()->getLocale()]) }}">@lang('menu.personal')</a></li>
                </ul><!-- /.list-unstyled footer-widget__links -->
            </div><!-- /.footer-widget -->
        </div><!-- /.col-xl-3 col-lg-3 col-md-4 col-sm-6 -->
        <div class="col-xl-3 col-lg-6 col-md-5 wow fadeInUp" data-wow-duration="1500ms"
            data-wow-delay="300ms">
            <div class="footer-widget footer-widget--contact">
                <h2 class="footer-widget__title">{{__('home.get')}}</h2><!-- /.footer-widget__title -->
                <ul class="list-unstyled footer-widget__info">
                    <li><a href="#">08692 Canicosa De La Sierra, TOLEDO</a>
                    </li>
                    <li>
                        <span class="footer-widget__info__icon"><i class="icon-paper-plane"></i></span>
                        <a href="mailto:contact@credixa.eu">contact@credixa.eu</a>
                    </li>
                    <li>
                        <span class="footer-widget__info__icon"><i class="icon-telephone"></i></span>
                        <a href="tel:+34613853614 ">+34 613 85 36 14 </a>
                    </li>
                </ul><!-- /.list-unstyled -->
            </div><!-- /.footer-widget -->
        </div><!-- /.col-xl-3 col-lg-6 col-md-5 -->
    </div><!-- /.row -->
</div><!-- /.container -->
</div><!-- /.main-footer__top -->
<div class="main-footer__bottom">
<div class="container">
    <div class="main-footer__bottom__inner">
        <div class="row gutter-y-40 align-items-center">
            <div class="col-md-5 wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="000ms">
                <div class=""></div>
            </div><!-- /.col-md-5 -->
            <div class="col-md-7 wow fadeInUp" data-wow-duration="1500ms" data-wow-delay="100ms">
                <div class="main-footer__bottom__copyright">
                    <p class="main-footer__copyright">
                       {{__('home.rights_reserved')}}
                    </p>
                </div><!-- /.main-footer__bottom__copyright -->
            </div><!-- /.col-md-7 -->
        </div><!-- /.row -->
    </div><!-- /.main-footer__inner -->
</div><!-- /.container -->
</div><!-- /.main-footer__bottom -->
