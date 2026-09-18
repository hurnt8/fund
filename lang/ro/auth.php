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

    'failed'   => 'Aceste date de autentificare nu corespund niciunui cont.',
    'password' => 'Parola furnizată este incorectă.',
    'throttle' => 'Prea multe încercări. Încercați din nou peste :seconds secunde.',

    'client_login_title'  => 'Spațiu Client',
    'client_login_sub'    => 'Conectați-vă pentru a accesa dosarele dumneavoastră',
    'client_brand_title'  => 'Spațiul dumneavoastră<br>client Aurenza Capital',
    'client_brand_sub'    => 'Urmăriți-vă cererile, gestionați-vă profilul și accesați toate documentele dumneavoastră în deplină siguranță.',

    'staff_login_title'   => 'Portal Administrație',
    'staff_login_sub'     => 'Rezervat exclusiv personalului autorizat',
    'staff_brand_title'   => 'Administrație<br>Aurenza Capital',
    'staff_brand_sub'     => 'Acces securizat la instrumentele de gestiune, urmărire a dosarelor și administrare a utilizatorilor.',

    'email'               => 'Adresă de email',
    'email_staff'         => 'Email profesional',
    'email_ph'            => 'dumneavoastra@exemplu.com',
    'email_ph_staff'      => 'agent@aurenzacapital.com',
    'password_label'      => 'Parolă',
    'remember'            => 'Ține-mă minte',
    'remember_staff'      => 'Rămâi conectat',
    'submit'              => 'Conectare',
    'submit_staff'        => 'Accesează panoul de control',
    'back_site'           => 'Înapoi la site',
    'staff_portal_link'   => 'Portal agent / administrator',
    'client_portal_link'  => 'Spațiu client',
    'staff_restricted'    => 'Acces restricționat — Personal autorizat',
    'staff_notice'        => 'Acest portal este rezervat agenților Aurenza Capital. Toate conectările sunt înregistrate.',
    'or_staff'            => 'Sunteți agent sau administrator?',
    'or_client'           => 'Sunteți client?',

    'stat_clients'        => 'Clienți mulțumiți',
    'stat_amount'         => 'Credit max / dosar',
    'stat_time'           => 'Răspuns garantat',
    'stat_years'          => "Ani de experiență",

    'feature_secure'      => 'Date criptate',
    'feature_currencies'  => '6 valute acceptate',
    'feature_certified'   => 'Autorizare europeană',
    'feature_fast'        => 'Răspuns în 24h',

    'role_superadmin'     => 'Super Administrator',
    'role_superadmin_sub' => 'Gestiune globală și roluri',
    'role_admin'          => 'Administrator',
    'role_admin_sub'      => 'Gestiunea dosarelor',

    // Identifier (email or phone)
    'identifier'          => 'Email sau telefon',
    'identifier_ph'       => 'email-ul@dvs.com sau +33...',
    'forgot_password'     => 'Ați uitat parola?',
    'portal_clients_only' => 'Acest portal este rezervat clienților.',

    // OTP page
    'otp_title'           => 'Verificare',
    'otp_heading'         => 'Cod de securitate',
    'otp_subtitle'        => 'V-am trimis un cod din 6 cifre la',
    'otp_enter'           => 'Introduceți codul primit prin email',
    'otp_verify_btn'      => 'Verifică',
    'otp_resend'          => 'Retrimite codul',
    'otp_resend_in'       => 'Retrimite în',
    'otp_back'            => 'Schimbă contul',
    'otp_verifying'       => 'Verificare în curs…',
    'otp_invalid'         => 'Cod incorect. Mai aveți :remaining încercare(i).',
    'otp_expired'         => 'Acest cod a expirat. Solicitați unul nou.',
    'otp_too_many'        => 'Prea multe încercări. Încercați din nou peste :seconds secunde.',
    'otp_resend_limit'    => 'Prea multe retrimiteri. Încercați din nou în câteva minute.',
    'otp_send_failed'     => 'Nu s-a putut trimite codul. Încercați din nou.',
    'otp_session_expired' => 'Sesiune expirată. Reconectați-vă.',
    'otp_resend_success'  => 'Cod nou trimis!',

    // Compte mémorisé
    'change_account' => 'Schimbă contul',

    // Compte bloqué
    'account_blocked'                     => 'Contul dumneavoastră este blocat din cauza prea multor încercări incorecte. Consultați emailul dumneavoastră pentru a primi linkul de deblocare.',
    'account_blocked_notified'            => 'Prea multe încercări incorecte. Contul dumneavoastră a fost blocat. Un link de deblocare v-a fost trimis prin email.',
    'account_unblocked'                   => 'Contul dumneavoastră a fost deblocat cu succes. Vă puteți conecta acum.',
    'unblock_invalid'                     => 'Acest link de deblocare este invalid sau a expirat. Contactați asistența.',

    'account_blocked_email_subject'       => 'Contul dumneavoastră Aurenza Capital a fost blocat',
    'account_blocked_email_title'         => 'Cont blocat temporar',
    'account_blocked_email_intro'         => 'Contul dumneavoastră a fost blocat temporar în urma mai multor încercări de conectare incorecte.',
    'account_blocked_email_reason_title'  => 'De ce acest blocaj?',
    'account_blocked_email_reason_body'   => '4 coduri OTP incorecte au fost introduse consecutiv în timpul unei încercări de conectare la contul dumneavoastră. Din motive de securitate, accesul a fost suspendat.',
    'account_blocked_email_btn'           => 'Deblochează contul meu',
    'account_blocked_email_fallback'      => 'Dacă butonul nu funcționează, copiați acest link în browserul dumneavoastră:',
    'account_blocked_email_notice'        => 'Dacă nu dumneavoastră ați inițiat aceste încercări, nu faceți clic pe acest link și contactați imediat asistența Aurenza Capital.',
    'account_blocked_email_footer'        => 'Link valabil 48 de ore.',

    // OTP email
    'otp_email_subject'      => 'Codul dumneavoastră de conectare — Aurenza Capital',
    'otp_email_title'        => 'Cod de verificare',
    'otp_email_intro'        => 'Iată codul dumneavoastră de conectare de unică folosință. Nu îl comunicați nimănui.',
    'otp_email_code_label'   => 'Codul dumneavoastră',
    'otp_email_expiry'       => 'Acest cod expiră în 10 minute.',
    'otp_email_notice_title' => 'Securitate importantă',
    'otp_email_notice_body'  => 'Aurenza Capital nu vă va solicita niciodată acest cod prin telefon sau mesaj. Dacă nu ați solicitat acest cod, ignorați acest email.',
    'otp_email_footer'       => 'Dacă nu ați solicitat acest cod, ignorați acest email.',

];
