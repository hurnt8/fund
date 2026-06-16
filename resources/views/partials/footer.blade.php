@php $locale = app()->getLocale(); @endphp

<footer class="site-footer">
    <div class="container">
        <div class="row g-4 gutter-y-50">

            <!-- Brand column -->
            <div class="col-lg-4 col-md-6">
                <a href="{{ route('home', ['locale' => $locale]) }}" class="d-inline-block mb-4">
                    <img src="{{ asset('assets/images/logo new.png') }}" alt="Credixa" class="footer-logo">
                </a>
                <p class="footer-desc">@lang('menu.footer_desc')</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter / X"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>

            <!-- Quick links -->
            <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                <h5 class="footer-heading">@lang('menu.quick_links')</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('home',     ['locale' => $locale]) }}">@lang('menu.home')</a></li>
                    <li><a href="{{ route('about',    ['locale' => $locale]) }}">@lang('menu.about')</a></li>
                    <li><a href="{{ route('services', ['locale' => $locale]) }}">@lang('menu.services')</a></li>
                    <li><a href="{{ route('faq',      ['locale' => $locale]) }}">@lang('menu.faq')</a></li>
                    <li><a href="{{ route('contact',  ['locale' => $locale]) }}">@lang('menu.contact')</a></li>
                    <li><a href="{{ route('loan',     ['locale' => $locale]) }}">@lang('menu.loan')</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-lg-2 col-md-6 col-sm-6 col-12">
                <h5 class="footer-heading">@lang('menu.services')</h5>
                <ul class="footer-links">
                    <li><a href="{{ route('services.personal', ['locale' => $locale]) }}">@lang('menu.personal')</a></li>
                    <li><a href="{{ route('services.home',     ['locale' => $locale]) }}">@lang('menu.home_loan')</a></li>
                    <li><a href="{{ route('services.auto',     ['locale' => $locale]) }}">@lang('menu.auto')</a></li>
                    <li><a href="{{ route('services.study',    ['locale' => $locale]) }}">@lang('menu.study')</a></li>
                    <li><a href="{{ route('services.business', ['locale' => $locale]) }}">@lang('menu.business')</a></li>
                    <li><a href="{{ route('services.bike',     ['locale' => $locale]) }}">@lang('menu.bike')</a></li>
                </ul>
            </div>

            <!-- Contact + newsletter -->
            <div class="col-lg-4 col-md-6">
                <h5 class="footer-heading">@lang('menu.contact')</h5>

                <div class="footer-contact-item">
                    <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                    <span>08692 Canicosa De La Sierra, Toledo, España</span>
                </div>
                <div class="footer-contact-item">
                    <div class="icon"><i class="fas fa-phone-alt"></i></div>
                    <a href="tel:+34613853614">+34 613 85 36 14</a>
                </div>
                <div class="footer-contact-item">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <a href="mailto:contact@credixa.eu">contact@credixa.eu</a>
                </div>

                <div class="mt-4">
                    <p class="text-sm mb-2" style="color:rgba(255,255,255,.45);font-size:.8125rem;">@lang('menu.newsletter_title')</p>
                    <form action="{{ route('subscribe.send') }}" method="POST" class="footer-newsletter">
                        @csrf
                        <input type="email" name="email" placeholder="@lang('menu.email_placeholder')" required>
                        <button type="submit" aria-label="S'abonner"><i class="fas fa-arrow-right"></i></button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Bottom bar -->
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} <a href="{{ route('home', ['locale' => $locale]) }}">Credixa</a>. @lang('menu.rights_reserved')</p>
            <p>
                <a href="{{ route('terms',   ['locale' => $locale]) }}">@lang('menu.terms')</a>
                <span style="color:rgba(255,255,255,.2);margin:0 .5rem">·</span>
                <a href="{{ route('privacy', ['locale' => $locale]) }}">@lang('menu.privacy')</a>
            </p>
        </div>
    </div>
</footer>
