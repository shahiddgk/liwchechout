<?php

namespace LW;

class ReCaptcha
{

    public function __construct()
    {
        add_filter( 'admin_init' , array($this, 'captchaSettingsFields') );
    }

    public function addCaptchaJsToFooter() {
        echo "<script src='https://www.google.com/recaptcha/api.js'></script>";
    }

    public function captchaSettingsFields() {
        // Create settings fields for the two keys used by reCAPTCHA
        register_setting( 'general', 'lummi-wild-se-recaptcha-activate' );
        register_setting( 'general', 'lummi-wild-se-recaptcha-site-key' );
        register_setting( 'general', 'lummi-wild-se-recaptcha-secret-key' );

        add_settings_field(
            'lummi-wild-se-recaptcha-activate',
            '<label for="lummi-wile-se-recaptcha-activate">' . __( 'reCAPTCHA Condition' , 'lummi-wild-se' ) . '</label>',
            array($this, 'renderCaptchaActivate'),
            'general'
        );

        add_settings_field(
            'lummi-wild-se-recaptcha-site-key',
            '<label for="lummi-wile-se-recaptcha-site-key">' . __( 'reCAPTCHA site key' , 'lummi-wild-se' ) . '</label>',
            array($this, 'renderCcaptchaSiteKeyField'),
            'general'
        );

        add_settings_field(
            'lummi-wild-se-recaptcha-secret-key',
            '<label for="lummi-wile-se-recaptcha-secret-key">' . __( 'reCAPTCHA secret key' , 'lummi-wild-se' ) . '</label>',
            array($this, 'renderCaptchaSecretKeyField'),
            'general'
        );
    }

    public function renderCaptchaActivate() {
        $is_activate = (get_option( 'lummi-wild-se-recaptcha-activate', '' )) ? 'checked' : null;
        $status = ($is_activate) ? 'Deactivate': 'Activate' ;
        echo '<input type="checkbox" value="1" id="lummi-wild-se-recaptcha-activate" name="lummi-wild-se-recaptcha-activate" '.$is_activate.'/><span>'.$status.'</span><br/><br/>
        Get <a href="https://www.google.com/recaptcha" target="_blank" style="color: blue;">Google reCAPTCHA</a>';
    }

    public function renderCcaptchaSiteKeyField() {
        $value = get_option( 'lummi-wild-se-recaptcha-site-key', '' );
        echo '<input type="text" id="lummi-wild-se-recaptcha-site-key" name="lummi-wild-se-recaptcha-site-key" value="' . esc_attr( $value ) . '" />';
    }

    public function renderCaptchaSecretKeyField() {
        $value = get_option( 'lummi-wild-se-recaptcha-secret-key', '' );
        echo '<input type="text" id="lummi-wild-se-recaptcha-secret-key" name="lummi-wild-se-recaptcha-secret-key" value="' . esc_attr( $value ) . '" />';
    }

    public function verifyRecaptcha() {
        // This field is set by the recaptcha widget if check is successful
        if ( isset ( $_POST['g-recaptcha-response'] ) ) {
            $captcha_response = $_POST['g-recaptcha-response'];
        } else {
            return null;
        }

        // Verify the captcha response from Google
        $response = wp_remote_post(
            'https://www.google.com/recaptcha/api/siteverify',
            array(
                'body' => array(
                    'secret' => get_option( 'lummi-wild-se-recaptcha-secret-key' ),
                    'response' => $captcha_response
                )
            )
        );

        $success = false;
        if ( $response && is_array( $response ) ) {
            $decoded_response = json_decode( $response['body'] );
            $success = $decoded_response->success;
        }

        return $success;
    }
}