@php $locale = app()->getLocale(); @endphp

<div class="service-sidebar">

    {{-- Services nav --}}
    <div class="service-sidebar__widget">
        <h3 class="service-sidebar__title">@lang('menu.services')</h3>
        <ul class="service-nav-list">
            @foreach ([
                ['route' => 'services.home',     'label' => 'menu.home_loan'],
                ['route' => 'services.personal', 'label' => 'menu.personal'],
                ['route' => 'services.auto',     'label' => 'menu.auto'],
                ['route' => 'services.business', 'label' => 'menu.business'],
                ['route' => 'services.study',    'label' => 'menu.study'],
                ['route' => 'services.bike',     'label' => 'menu.bike'],
            ] as $svc)
            <li class="service-nav-list__item">
                <a href="{{ route($svc['route'], ['locale' => $locale]) }}"
                   class="{{ ($currentRoute ?? '') === $svc['route'] ? 'active' : '' }}">
                    @lang($svc['label'])
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Contact card --}}
    <div class="contact-widget">
        <div class="contact-widget__icon"><i class="fas fa-phone-alt"></i></div>
        <h4>@lang('contact.phone_title')</h4>
        <p>Lun–Sam 8h00 – 18h00</p>
        <a href="tel:+34613853614" class="contact-widget__phone">+34 613 85 36 14</a>
        <a href="{{ route('contact', ['locale' => $locale]) }}" class="btn-primary w-100 justify-content-center">
            <i class="fas fa-envelope"></i> @lang('menu.contact')
        </a>
    </div>

</div>
