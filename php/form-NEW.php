<?php
$recipient = 'ara@saxonara.com'; 
$subject   = 'FORM - SpanishGuitar.cat'; 
$success   = 'Your message was sent successful. Thanks.';
$error     = 'Sorry. We were unable to send your message.';
$invalid   = 'Validation errors occurred. Please confirm the fields and submit it again.';

if ( ! empty( $_POST ) ) {

    require_once('recaptcha.php');

    // Validem el correu del remitent
    $from_user = filter_var( $_POST['email'] ?? '', FILTER_VALIDATE_EMAIL );

    // Lògica de Captcha (assegura't que $reCAPTCHA vingui de recaptcha.php)
    $errCaptcha = ( empty( $reCAPTCHA['success'] ) ) ? true : '';

    $errFields = array();

    foreach( $_POST as $key => $value ) {
        if ( $key != 'section' && $key != 'reCAPTCHA' ) {
            if ( $key == 'email' ) {
                $validation = filter_var( $value, FILTER_VALIDATE_EMAIL );
            } else {                
                $validation = ! empty( trim($value) );
            }
            
            if ( ! $validation ) {
                $errFields[$key] = true;
            } 
        }
    }

    if ( empty( $errCaptcha ) && count( $errFields ) === 0 ) {
            
        // CAPÇALERES (Millorat per evitar SPAM)
        $header  = "From: SpanishGuitar Web <no-reply@spanishguitar.cat>\r\n"; // Canvia per un correu del teu domini
        $header .= "Reply-To: " . $from_user . "\r\n"; 
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-type: text/html; charset=UTF-8\r\n";

        // COS DEL MISSATGE (Sintaxi corregida)
        $body  = '<table style="padding: 35px; background-color: #f5f5f5; font-family: sans-serif; font-size: 1rem; text-align: left; border-radius: 4px;">';
        $body .= '<tr><th style="font-size: 1.5rem; font-weight: 600; color: #1E50BC; padding-bottom: 20px;">'.$subject.'</th></tr>';
        $body .= '<tr><td>';

        foreach( $_POST as $key => $value ) {
            if ( $key != 'section' && $key != 'reCAPTCHA' ) {
                // Netegem el valor per seguretat
                $clean_value = htmlspecialchars(stripslashes(trim($value)));
                $body .= '<p style="margin-bottom: 10px;"><b>' . str_replace( '-', ' ', ucfirst( $key ) ) . '</b>: ' . $clean_value . '</p>';
            }
        }

        $body .= '</td></tr>';        
        $body .= '</table>';

        $mail = mail( $recipient, $subject, $body, $header );

        if ( $mail ) {
            $response = array('status' => 'success', 'info' => $success);
        } else {
            $response = array('status' => 'fail', 'info' => $error);
        }

    } else {
        $response = array(
            'status'  => 'invalid',
            'info'    => $invalid,
            'captcha' => $errCaptcha,
            'fields'  => $errFields,
            'errors'  => count( $errFields )
        );
    }

    header('Content-Type: application/json');
    echo json_encode( $response );
    exit;
}