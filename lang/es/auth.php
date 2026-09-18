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

    'failed'   => 'Estas credenciales no coinciden con nuestros registros.',
    'password' => 'La contraseña proporcionada es incorrecta.',
    'throttle' => 'Demasiados intentos. Vuelva a intentarlo en :seconds segundos.',

    'client_login_title'  => 'Área de Clientes',
    'client_login_sub'    => 'Inicie sesión para acceder a sus expedientes',
    'client_brand_title'  => 'Su espacio<br>cliente Aurenza Capital',
    'client_brand_sub'    => 'Siga sus solicitudes, gestione su perfil y acceda a todos sus documentos de forma segura.',

    'staff_login_title'   => 'Portal de Administración',
    'staff_login_sub'     => 'Exclusivamente para personal autorizado',
    'staff_brand_title'   => 'Administración<br>Aurenza Capital',
    'staff_brand_sub'     => 'Acceso seguro a herramientas de gestión, seguimiento de expedientes y administración de usuarios.',

    'email'               => 'Correo electrónico',
    'email_staff'         => 'Email profesional',
    'email_ph'            => 'usted@ejemplo.com',
    'email_ph_staff'      => 'agente@aurenzacapital.com',
    'password_label'      => 'Contraseña',
    'remember'            => 'Recordarme',
    'remember_staff'      => 'Mantener sesión',
    'submit'              => 'Iniciar sesión',
    'submit_staff'        => 'Acceder al panel',
    'back_site'           => 'Volver al sitio',
    'staff_portal_link'   => 'Portal agente / administrador',
    'client_portal_link'  => 'Área de clientes',
    'staff_restricted'    => 'Acceso restringido — Solo personal autorizado',
    'staff_notice'        => 'Este portal está reservado para el personal de Aurenza Capital. Todos los accesos quedan registrados.',
    'or_staff'            => '¿Es agente o administrador?',
    'or_client'           => '¿Es cliente?',

    'stat_clients'        => 'Clientes satisfechos',
    'stat_amount'         => 'Préstamo máx. / expediente',
    'stat_time'           => 'Respuesta garantizada',
    'stat_years'          => 'Años de experiencia',

    'feature_secure'      => 'Datos cifrados',
    'feature_currencies'  => '6 divisas aceptadas',
    'feature_certified'   => 'Certificación UE',
    'feature_fast'        => 'Respuesta en 24h',

    'role_superadmin'     => 'Super Administrador',
    'role_superadmin_sub' => 'Gestión global y roles',
    'role_admin'          => 'Administrador',
    'role_admin_sub'      => 'Gestión de expedientes',

    'identifier'          => 'Email o teléfono',
    'identifier_ph'       => 'su@email.com o +34...',
    'forgot_password'     => '¿Olvidó su contraseña?',
    'portal_clients_only' => 'Este portal está reservado para clientes.',

    'otp_title'           => 'Verificación',
    'otp_heading'         => 'Código de seguridad',
    'otp_subtitle'        => 'Enviamos un código de 6 dígitos a',
    'otp_enter'           => 'Introduzca el código recibido por email',
    'otp_verify_btn'      => 'Verificar',
    'otp_resend'          => 'Reenviar código',
    'otp_resend_in'       => 'Reenviar en',
    'otp_back'            => 'Cambiar cuenta',
    'otp_verifying'       => 'Verificando…',
    'otp_invalid'         => 'Código incorrecto. Le quedan :remaining intento(s).',
    'otp_expired'         => 'Este código ha caducado. Solicite uno nuevo.',
    'otp_too_many'        => 'Demasiados intentos. Vuelva a intentarlo en :seconds segundos.',
    'otp_resend_limit'    => 'Demasiados reenvíos. Vuelva a intentarlo en unos minutos.',
    'otp_send_failed'     => 'No se pudo enviar el código. Inténtelo de nuevo.',
    'otp_session_expired' => 'Sesión caducada. Por favor, inicie sesión de nuevo.',
    'otp_resend_success'  => '¡Nuevo código enviado!',

    // Cuenta recordada
    'change_account' => 'Cambiar de cuenta',

    // Cuenta bloqueada
    'account_blocked'                     => 'Su cuenta ha sido bloqueada tras demasiados intentos incorrectos. Consulte su email para recibir el enlace de desbloqueo.',
    'account_blocked_notified'            => 'Demasiados intentos incorrectos. Su cuenta ha sido bloqueada. Se ha enviado un enlace de desbloqueo a su email.',
    'account_unblocked'                   => 'Su cuenta ha sido desbloqueada con éxito. Ahora puede iniciar sesión.',
    'unblock_invalid'                     => 'Este enlace de desbloqueo no es válido o ha caducado. Contacte con el soporte.',

    'account_blocked_email_subject'       => 'Su cuenta Aurenza Capital ha sido bloqueada',
    'account_blocked_email_title'         => 'Cuenta temporalmente bloqueada',
    'account_blocked_email_intro'         => 'Su cuenta ha sido temporalmente bloqueada tras varios intentos de inicio de sesión incorrectos.',
    'account_blocked_email_reason_title'  => '¿Por qué este bloqueo?',
    'account_blocked_email_reason_body'   => 'Se introdujeron 4 códigos OTP incorrectos consecutivos durante un intento de acceso. Por seguridad, el acceso ha sido suspendido.',
    'account_blocked_email_btn'           => 'Desbloquear mi cuenta',
    'account_blocked_email_fallback'      => 'Si el botón no funciona, copie este enlace en su navegador:',
    'account_blocked_email_notice'        => 'Si no ha realizado estos intentos, no haga clic en este enlace y contacte inmediatamente con el soporte de Aurenza Capital.',
    'account_blocked_email_footer'        => 'Enlace válido durante 48 horas.',

    'otp_email_subject'      => 'Su código de acceso — Aurenza Capital',
    'otp_email_title'        => 'Código de verificación',
    'otp_email_intro'        => 'Aquí tiene su código de acceso de un solo uso. No lo comparta con nadie.',
    'otp_email_code_label'   => 'Su código',
    'otp_email_expiry'       => 'Este código caduca en 10 minutos.',
    'otp_email_notice_title' => 'Aviso de seguridad importante',
    'otp_email_notice_body'  => 'Aurenza Capital nunca le pedirá este código por teléfono o mensaje. Si no ha solicitado este código, ignore este email.',
    'otp_email_footer'       => 'Si no ha solicitado este código, ignore este email.',

];
