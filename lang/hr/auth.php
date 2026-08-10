<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed'   => 'Ovi podaci za prijavu ne odgovaraju nijednom računu.',
    'password' => 'Unesena lozinka je netočna.',
    'throttle' => 'Previše pokušaja. Pokušajte ponovno za :seconds sekundi.',

    'client_login_title'  => 'Klijentski prostor',
    'client_login_sub'    => 'Prijavite se za pristup vašim dosjeima',
    'client_brand_title'  => 'Vaš klijentski<br>prostor Credixa',
    'client_brand_sub'    => 'Pratite svoje zahtjeve, upravljajte svojim profilom i pristupite svim svojim dokumentima u potpunoj sigurnosti.',

    'staff_login_title'   => 'Administratorski portal',
    'staff_login_sub'     => 'Rezervirano isključivo za ovlašteno osoblje',
    'staff_brand_title'   => 'Administracija<br>Credixa',
    'staff_brand_sub'     => 'Siguran pristup alatima za upravljanje, praćenje dosjea i administraciju korisnika.',

    'email'               => 'E-mail adresa',
    'email_staff'         => 'Poslovni e-mail',
    'email_ph'            => 'vi@primjer.com',
    'email_ph_staff'      => 'agent@credixa.eu',
    'password_label'      => 'Lozinka',
    'remember'            => 'Zapamti me',
    'remember_staff'      => 'Ostani prijavljen',
    'submit'              => 'Prijavi se',
    'submit_staff'        => 'Pristupi nadzornoj ploči',
    'back_site'           => 'Povratak na web stranicu',
    'staff_portal_link'   => 'Portal za agente / administratore',
    'client_portal_link'  => 'Klijentski prostor',
    'staff_restricted'    => 'Ograničen pristup — Ovlašteno osoblje',
    'staff_notice'        => 'Ovaj portal je rezerviran za Credixa agente. Sve prijave se bilježe.',
    'or_staff'            => 'Jeste li agent ili administrator?',
    'or_client'           => 'Jeste li klijent?',

    'stat_clients'        => 'Zadovoljni klijenti',
    'stat_amount'         => 'Maks. zajam / dosje',
    'stat_time'           => 'Zajamčeni odgovor',
    'stat_years'          => "Godina iskustva",

    'feature_secure'      => 'Šifrirani podaci',
    'feature_currencies'  => '6 prihvaćenih valuta',
    'feature_certified'   => 'Europsko odobrenje',
    'feature_fast'        => 'Odgovor u 24h',

    'role_superadmin'     => 'Super Administrator',
    'role_superadmin_sub' => 'Globalno upravljanje i uloge',
    'role_admin'          => 'Administrator',
    'role_admin_sub'      => 'Upravljanje dosjeima',

    // Identifier (email or phone)
    'identifier'          => 'E-mail ili telefon',
    'identifier_ph'       => 'vas@email.com ili +33...',
    'forgot_password'     => 'Zaboravili ste lozinku?',
    'portal_clients_only' => 'Ovaj portal je rezerviran za klijente.',

    // OTP page
    'otp_title'           => 'Provjera',
    'otp_heading'         => 'Sigurnosni kod',
    'otp_subtitle'        => 'Poslali smo 6-znamenkasti kod na',
    'otp_enter'           => 'Unesite kod primljen putem e-maila',
    'otp_verify_btn'      => 'Provjeri',
    'otp_resend'          => 'Ponovno pošalji kod',
    'otp_resend_in'       => 'Ponovno pošalji za',
    'otp_back'            => 'Promijeni račun',
    'otp_verifying'       => 'Provjera u tijeku…',
    'otp_invalid'         => 'Netočan kod. Preostalo vam je :remaining pokušaj(a).',
    'otp_expired'         => 'Ovaj kod je istekao. Zatražite novi.',
    'otp_too_many'        => 'Previše pokušaja. Pokušajte ponovno za :seconds sekundi.',
    'otp_resend_limit'    => 'Previše ponovnih slanja. Pokušajte ponovno za nekoliko minuta.',
    'otp_send_failed'     => 'Slanje koda nije uspjelo. Pokušajte ponovno.',
    'otp_session_expired' => 'Sesija je istekla. Ponovno se prijavite.',
    'otp_resend_success'  => 'Novi kod poslan!',

    // Compte mémorisé
    'change_account' => 'Promijeni račun',

    // Compte bloqué
    'account_blocked'                     => 'Vaš račun je blokiran zbog previše netočnih pokušaja. Provjerite svoj e-mail kako biste primili poveznicu za deblokiranje.',
    'account_blocked_notified'            => 'Previše netočnih pokušaja. Vaš račun je blokiran. Poveznica za deblokiranje poslana vam je e-mailom.',
    'account_unblocked'                   => 'Vaš račun je uspješno deblokiran. Sada se možete prijaviti.',
    'unblock_invalid'                     => 'Ova poveznica za deblokiranje nije valjana ili je istekla. Kontaktirajte podršku.',

    'account_blocked_email_subject'       => 'Vaš Credixa račun je blokiran',
    'account_blocked_email_title'         => 'Račun privremeno blokiran',
    'account_blocked_email_intro'         => 'Vaš račun je privremeno blokiran nakon nekoliko netočnih pokušaja prijave.',
    'account_blocked_email_reason_title'  => 'Zašto je ovaj račun blokiran?',
    'account_blocked_email_reason_body'   => '4 netočna OTP koda uneseni su uzastopno tijekom pokušaja prijave na vaš račun. Iz sigurnosnih razloga, pristup je suspendiran.',
    'account_blocked_email_btn'           => 'Deblokiraj moj račun',
    'account_blocked_email_fallback'      => 'Ako gumb ne funkcionira, kopirajte ovu poveznicu u svoj preglednik:',
    'account_blocked_email_notice'        => 'Ako niste vi pokrenuli ove pokušaje, nemojte kliknuti na ovu poveznicu i odmah kontaktirajte Credixa podršku.',
    'account_blocked_email_footer'        => 'Poveznica vrijedi 48 sati.',

    // OTP email
    'otp_email_subject'      => 'Vaš kod za prijavu — Credixa',
    'otp_email_title'        => 'Kod za provjeru',
    'otp_email_intro'        => 'Evo vašeg jednokratnog koda za prijavu. Nemojte ga nikome dijeliti.',
    'otp_email_code_label'   => 'Vaš kod',
    'otp_email_expiry'       => 'Ovaj kod istječe za 10 minuta.',
    'otp_email_notice_title' => 'Važna sigurnosna napomena',
    'otp_email_notice_body'  => 'Credixa vas nikada neće tražiti ovaj kod putem telefona ili poruke. Ako niste zatražili ovaj kod, zanemarite ovaj e-mail.',
    'otp_email_footer'       => 'Ako niste zatražili ovaj kod, zanemarite ovaj e-mail.',

];
