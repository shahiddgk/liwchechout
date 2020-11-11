<?php

namespace LW;

class Forms
{
    private $recaptcha = null;

    private $recaptcha_activate = null;

    private $request_club = null;

    public function __construct(){
        $this->recaptcha = new \LW\ReCaptcha();

        if($this->recaptcha != ''){
            $this->recaptcha_activate = get_option( 'lummi-wild-se-recaptcha-activate', null );
        }
    }

    /**
     * Register action, filters and shortcodes
     */
    public function applySettingsForms(){
        add_action( 'init', array($this,'doOutputBuffer'));
        add_action( 'login_form_login', array( $this, 'redirectToLogin' ) );
        add_action( 'wp_logout', array( $this, 'redirectAfterLogout' ) );
        add_filter( 'authenticate', array( $this, 'maybeRedirectAuthenticate' ), 101, 3 );
        add_filter( 'login_redirect', array( $this, 'redirectAfterLogin' ), 10, 3 );
        add_action( 'login_form_register', array( $this, 'redirectToRegister' ) );
        add_action( 'login_form_register', array($this, 'doRegisterUser') );
        add_action( 'login_form_lostpassword', array($this, 'redirectToLostpassword' ) );
        add_action( 'login_form_lostpassword', array($this, 'doPasswordLost') );
        add_filter( 'retrieve_password_message', array($this, 'replaceRetrievePasswordMessage'), 10, 4 );
        add_action( 'login_form_rp', array($this, 'redirectToPasswordReset' ) );
        add_action( 'login_form_resetpass', array( $this, 'redirectToPasswordReset') );
        add_action( 'login_form_rp', array( $this, 'doPasswordReset' ) );
        add_action( 'login_form_resetpass', array($this, 'doPasswordReset') );
        add_shortcode( 'lummi_login_form', array($this, 'renderLoginForm') );
        add_shortcode('lummi_register_form',array($this,'renderRegisterForm'));
        add_shortcode( 'lummi_lost_password', array($this, 'renderPasswordLostForm'));
        add_shortcode( 'lummi_reset_password', array($this, 'renderPasswordResetForm') );
        add_action( 'init', array($this,'rewriteRegisterPageIndividual') );
        add_filter( 'query_vars', array($this,'varRegisterPageIndividual') );
        add_action( 'template_redirect', array($this,'catchFormRegisterPageIndividual'));
        add_action( 'init',array($this,'isIndividualRegistration'));
        add_action('wp_enqueue_scripts',array($this,'setCookieForIndividualRegistration'));
        remove_filter( 'authenticate', array($this,'wp_authenticate_username_password'), 20);
        add_filter( 'authenticate', array($this,'dr_email_login_authenticate'), 20, 3 );
    }

    /**
     * Clear buffer to prevent header redirect problem
     */
    public function doOutputBuffer() {
        ob_start();
    }

    /**
     * Rewrite for individual register for club
     */
    function rewriteRegisterPageIndividual()
    {
        add_rewrite_rule('lwu-register/([^/]+)/?$','page=lwu-register&club=$matches[2]','top');
    }

    function varRegisterPageIndividual( $vars )
    {
        $vars[] = 'club';
        return $vars;
    }

    function catchFormRegisterPageIndividual()
    {
        if( get_query_var('club') && is_page('lwu-register')) {
            add_filter('template_include', function() {
                return LW_PLUGIN_DIR . 'client_templates/club_register.php';
            });
        }
    }
    /**
     * Rendering login form by template and set attributes
     *
     * @param $attributes
     * @param null $content
     * @return string|void
     */
    public function renderLoginForm( $attributes, $content = null ) {

        $default_attributes = array( 'show_title' => false );
        $attributes = shortcode_atts( $default_attributes, $attributes );
        $show_title = $attributes['show_title'];

        if ( is_user_logged_in() ) {
            return __( 'You are already signed in.', 'lummi-wild-se' );
        }

        $attributes['redirect'] = '';
        if ( isset( $_REQUEST['redirect_to'] ) ) {
            $attributes['redirect'] = wp_validate_redirect( $_REQUEST['redirect_to'], $attributes['redirect'] );
        }

        // Error messages
        $errors = array();
        if ( isset( $_REQUEST['login'] ) ) {
            $error_codes = explode( ',', $_REQUEST['login'] );

            foreach ( $error_codes as $code ) {
                $errors[] = $this->getErrorMessage( $code );
            }
        }

        $attributes['errors'] = $errors;
        $attributes['logged_out'] = isset( $_REQUEST['logged_out'] ) && $_REQUEST['logged_out'] == true;
        $attributes['registered'] = isset( $_REQUEST['registered'] );
        $attributes['lost_password_sent'] = isset( $_REQUEST['checkemail'] ) && $_REQUEST['checkemail'] == 'confirm';
        $attributes['password_updated'] = isset( $_REQUEST['password'] ) && $_REQUEST['password'] == 'changed';

        return LW_Template::getTemplate( 'client_templates','login_form', $attributes, false);
    }

    /**
     * Redirect user to custom login form
     */
    public function redirectToLogin() {
        if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {

            $login_url = home_url( 'lwu-login' );
            wp_redirect( $login_url);
            exit;
        }
    }

    /**
     * @param $user
     * @param $username
     * @param $password
     * @return mixed
     */
    public function maybeRedirectAuthenticate( $user, $username, $password ) {
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            if ( is_wp_error( $user ) ) {
                $error_codes = join( ',', $user->get_error_codes() );

                $login_url = home_url( 'lwu-login' );
                $login_url = add_query_arg( 'login', $error_codes, $login_url );

                wp_redirect( $login_url );
                exit;
            }
        }

        return $user;
    }

    function dr_email_login_authenticate( $user, $username, $password ) {
        if ( is_a( $user, 'WP_User' ) )
            return $user;

        if ( !empty( $username ) ) {
            $username = str_replace( '&', '&amp;', stripslashes( $username ) );
            $user = get_user_by( 'email', $username );
            if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status )
                $username = $user->user_login;
        }

        return wp_authenticate_username_password( null, $username, $password );
    }

    /**
     * Redirect user after logout
     */
    public function redirectAfterLogout(){
        $redirect_url = home_url( 'lwu-login?logged_out=true' );
        $woocart = new \WC_Cart();
        $woocart->empty_cart();
        if( isset($_COOKIE['HCLUB_ID'])){
            unset($_COOKIE['HCLUB_ID']);
            setcookie('HCLUB_ID', null, -1, '/');
        }
        wp_safe_redirect( $redirect_url );
        exit;
    }

    /**
     * @param $redirect_to
     * @param $requested_redirect_to
     * @param $user
     * @return string|void
     */
    public function redirectAfterLogin($redirect_to, $requested_redirect_to, $user){

        $is_closed_club = \LW\Settings::is_closed_club();

        $redirect_url = home_url();

        if ( ! isset( $user->ID ) ) {
            return $redirect_url;
        }

        $user_meta = get_user_meta($user->ID);
        $the_club = get_term_meta($user_meta["user_club"][0]);

        if (!$the_club["default_club"][0]){
            $woocart = new \WC_Cart();
            $woocart->empty_cart();
        }
            $status = 0;

            $now = current_time('m/d/Y h:i a', $gmt = 0);
            $date_now = strtotime($now);

            $dc = preg_split('/\s+/', $the_club["clubs_close_date"][0]);
            $date1 = str_ireplace('-','/',$dc[0]);
            $cldate = strtotime($date1);

            $do = preg_split('/\s+/', $the_club["clubs_open_date"][0]);
            $date2 = str_ireplace('-','/',$do[0]);
            $opdate = strtotime($date2);

            if ($cldate < $date_now && $opdate < $date_now) {
                $status = 1;
            }
            if ($opdate > $date_now && $cldate > $date_now) {
                $status = 1;
            }

            if(! user_can( $user, 'administrator' )){
                if($status === 1 || $the_club["clubs_active"][0] == 'off' ){
                	$redirect = home_url('/inactive-club/');
                    return wp_validate_redirect($redirect, site_url());
                }
            }

        if ( user_can( $user, 'administrator' ) || (user_can($user,'club_manager_options') && $user_meta["user_club"][0]) ) {
            // Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard.
            if ( $requested_redirect_to == '?wpe-login=liw' ) {
            	if(user_can($user,'club_manager_options')){
		            $redirect_url = admin_url('admin.php?page=manage-club-home');
	            }else{
		            $redirect_url = admin_url();
	            }

            } else {
                $redirect_url = $requested_redirect_to;
            }
        }else {

            // Non-admin users always go to their account page after login
            $shop_page_id = get_option('woocommerce_shop_page_id');
            $post = get_post($shop_page_id);
            $slug = $post->post_name;
            $redirect_url = home_url( $slug );
        }

        return wp_validate_redirect( $redirect_url, site_url() );
    }

    // Start register form methods
    /**
     * @param $attributes
     * @param null $content
     * @return mixed
     */
    public function renderRegisterForm($attributes, $content = null){
        $default_attributes = array( 'show_title' => false, 'clubs_select_menu' => false);
        $attributes = shortcode_atts( $default_attributes, $attributes );

        if ( ! get_option( 'users_can_register' ) ) {
            return __( 'Registering new users is currently not allowed.', 'lummi-wild-se' );
        } else {

            $attributes['errors'] = array();
            if ( isset( $_REQUEST['register-errors'] ) ) {
                $error_codes = explode( ',', $_REQUEST['register-errors'] );
                foreach ( $error_codes as $error_code ) {
                    $attributes['errors'][] = $this->getErrorMessage( $error_code );
                }
            }

            // Add reCAPTCHA script in footer if activated from admin settings
            if($this->recaptcha_activate != ''){
                $attributes['recaptcha_activate'] = $this->recaptcha_activate;
                $attributes['recaptcha_site_key'] = get_option( 'lummi-wild-se-recaptcha-site-key', null );
                add_action( 'wp_print_footer_scripts', array($this->recaptcha, 'addCaptchaJsToFooter') );
            }

            if($this->request_club){
                $attributes['clubs'] = get_term_by('slug',$this->request_club,'clubs',ARRAY_A);
            }else{
                $attributes['clubs'] = get_terms('clubs',array('hide_empty' => false,'fields' => 'id=>name'));
            }

            return LW_Template::getTemplate( 'client_templates','register_form', $attributes, false);
        }
    }

    /**
     * Get template for individual registration
     */
    public function isIndividualRegistration(){
        if( isset($_REQUEST['club']) ){
            $club = get_term_by('slug',$_REQUEST['club'],'clubs',ARRAY_A);
            if( $club["slug"] === $_REQUEST['club']){
                $this->request_club =  $club["slug"];
            }else{
                wp_redirect(home_url('404'));
                exit;
            }
        }
    }

    /**
     * Send slug to js script and set cookie
     */
    public function setCookieForIndividualRegistration(){
        if($this->request_club){
            wp_enqueue_script('spcreg',LW_PLUGIN_URL.'additional/js/spcreg.js',array('jquery'),'',true);
            wp_localize_script('spcreg','regi',array(
                'club_cook' => $this->request_club
            ));
        }
    }

    /**
     * Redirect user to custom form template
     */
    public function redirectToRegister() {
        if ( 'GET' == $_SERVER['REQUEST_METHOD']) {
            wp_redirect( home_url( 'lwu-register' ) );
            exit;
        }
    }

    /**
     * Finalize registration and redirect to login
     */
    public function doRegisterUser() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {

            $redirect_url = home_url( 'lwu-register');

            if ( ! get_option( 'users_can_register' ) ) {
                // Registration closed, display error
                $redirect_url = add_query_arg( 'register-errors', 'closed', $redirect_url );
            } else {
                $username = $_POST['user_login'];
                $email = $_POST['email'];
                $first_name = sanitize_text_field( $_POST['first_name'] );
                $last_name = sanitize_text_field( $_POST['last_name'] );
                $user_attr['user_club'] = $_POST['user_club'];
                if($this->recaptcha_activate != ''){
                    $user_attr['captcha'] = $this->recaptcha->verifyRecaptcha();
                }

                $result = $this->registerUser( $username, $email, $first_name, $last_name, $user_attr );
                if ( is_wp_error( $result ) ) {
                    $errors = join( ',', $result->get_error_codes());
                    if($_COOKIE['club_reg']){
                        $redirect_url = home_url('lwu-register/?club='.$_COOKIE['club_reg'].'&register-errors='.$errors);
                    }else{
                        // Parse errors into a string and append as parameter to redirect
                        $redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
                    }
                } else {
                    // Success, redirect to login page.
                    $redirect_url = home_url( 'lwu-login' );
                    $redirect_url = add_query_arg( 'registered', $email, $redirect_url );
                }
            }
            wp_redirect( $redirect_url );
            exit;
        }
    }


    /**
     * Submit errors to forms
     * @param $error_code
     * @return string|void
     */
    private function getErrorMessage( $error_code ) {
        switch ( $error_code ) {
            case 'empty_username':
                return __( 'You do have an email address, right?', 'lummi-wild-se' );
            case 'empty_password':
                return __( 'You need to enter a password to login.', 'lummi-wild-se' );
            case 'invalid_username':
                return __(
                    "We don't have any users with that email address. Maybe you used a different one when signing up?",
                    'lummi-wild-se'
                );
            case 'incorrect_password':
                $err = __(
                    "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
                    'lummi-wild-se'
                );
                return sprintf( $err, home_url('lwu-lostpassword') );
            case 'empty_registered_user' :
                return __( 'Please enter your username. Longer than 2 characters.', 'lummi-wild-se' );
            case 'exists_register_user' :
                return __('An account exists with this username.', 'lummi-wild-se' );
            case 'email':
                return __( 'The email address you entered is not valid.', 'lummi-wild-se' );
            case 'email_exists':
                return __( 'An account exists with this email address.', 'lummi-wild-se' );
            case 'empty_first_name' :
                return __( 'Please enter a valid First Name.', 'lummi-wild-se' );
            case 'empty_last_name' :
                return __( 'Please enter a valid Last Name.', 'lummi-wild-se' );

            case 'closed':
                return __( 'Registering new users is currently not allowed.', 'lummi-wild-se' );
            case 'captcha' :
                return __( 'The Google reCAPTCHA check failed. Are you a robot?', 'lummi-wild-se' );
            case 'empty_username':
                return __( 'You need to enter your email address to continue.', 'lummi-wild-se' );

            case 'invalid_email':
            case 'invalidcombo':
                return __( 'There are no users registered with this email address.', 'lummi-wild-se' );
            case 'expiredkey':
            case 'invalidkey':
                return __( 'The password reset link you used is not valid anymore.', 'lummi-wild-se' );

            case 'password_reset_mismatch':
                return __( "The two passwords you entered don't match.", 'lummi-wild-se' );

            case 'password_reset_empty':
                return __( "Sorry, we don't accept empty passwords.", 'lummi-wild-se' );
            default:
                break;
        }

    }

    /**
     * Add new user in database and send email with user data for entry
     *
     * @param $username
     * @param $email
     * @param $first_name
     * @param $last_name
     * @param null $user_attr
     * @return int|\WP_Error
     */
    private function registerUser( $username, $email, $first_name, $last_name, $user_attr = null ) {

        $errors = new \WP_Error();

        if(empty($username) || strlen($username) < 2){
            $errors->add( 'empty_registered_user', $this->getErrorMessage( 'empty_registered_user' ) );
            //return $errors;
        }

        if(username_exists($username)){
            $errors->add( 'exists_register_user', $this->getErrorMessage( 'exists_register_user' ) );
            //return $errors;
        }

        if ( ! is_email( $email ) ) {
            $errors->add( 'email', $this->getErrorMessage( 'email' ) );
            //return $errors;
        }

        if ( username_exists( $email ) || email_exists( $email ) ) {
            $errors->add('email_exists', $this->getErrorMessage('email_exists'));
        }

        if( empty($first_name)){
            $errors->add('empty_first_name', $this->getErrorMessage('empty_first_name'));
        }
        if( empty($last_name)){
            $errors->add('empty_last_name', $this->getErrorMessage('empty_last_name'));
        }
        if($this->recaptcha_activate != ''){
            if(!$user_attr['captcha']){
                $errors->add('captcha', $this->getErrorMessage('captcha'));
            }
        }

        $err = $errors->get_error_messages();

        if($err){
            return $errors;
        }

            // Generate a password that send the user after successful registration
        $password = wp_generate_password( 6, false,false );

        $user_data = array(
            'user_login'    => $username,
            'user_email'    => $email,
            'user_pass'     => $password,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'nickname'      => $first_name,
            'role'          => 'club-member'
        );


        $user_id = wp_insert_user( $user_data );

        if ( !is_wp_error( $user_id )) {

            if($user_attr['user_club'] != ''){
                update_user_meta( $user_id, 'user_club', $user_attr['user_club']);
                update_user_meta( $user_id, 'billing_country', 'US');
                update_user_meta( $user_id, 'shipping_country', 'US');
            }
            
            $this->sendEmailToNewUser($user_data);

            $this->sendNewUserDataToAdmin($user_data);
        }

        return $user_id;
    }

    /**
     * Send success message to new user
     * @param $user_data
     */
    private function sendEmailToNewUser($user_data){
        $to = $user_data['user_email'];
        $subject = get_bloginfo('name').' Success Registration';
        $club = null;

        if($user_data['user_club']){
            $club = 'Request for membership in club: '.$user_data['user_club'].'';
        }

        $message = 'Welcome '.$user_data['first_name'].' '.$user_data['last_name'].' to our website. 
        Login info:
        Username: '.$user_data['user_login'].'
        Password: '.$user_data['user_pass'].'
        You can login at the following link: '.home_url('lwu-login').'
        '.$club.'';

        if(is_email($to)){
            wp_mail($to,$subject,$message);
        }
    }

    /**
     * Send data for new user to admin
     * @param $user_data
     */
    private function sendNewUserDataToAdmin($user_data){
        $to = get_option('admin_email');
        $subject = bloginfo('name').'New User Registration';
        $message = 'New user registered in '.get_bloginfo('name').' 
        Login info:
        Username: '.$user_data['user_login'].'
        Email: '.$user_data['user_email'].'
        Name: '.$user_data['first_name'].' '.$user_data['last_name'].';
        Sign in for more information: '.home_url('lwu-login').'';
        if(is_email($to)){
            wp_mail($to,$subject,$message);
        }
    }

    // Lost password methods

    /**
     * Redirect to custom form for lost password.
     */
    public function redirectToLostpassword(){
        if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {

            wp_redirect( home_url( 'lwu-lostpassword' ) );
            exit;
        }
    }

    /**
     * Adding template and application attributes.
     *
     * @param $attributes
     * @param null $content
     * @return string
     */
    public function renderPasswordLostForm( $attributes, $content = null ) {
        $default_attributes = array( 'show_title' => false );
        $attributes = shortcode_atts( $default_attributes, $attributes );

        $attributes['errors'] = array();
        if ( isset( $_REQUEST['errors'] ) ) {
            $error_codes = explode( ',', $_REQUEST['errors'] );

            foreach ( $error_codes as $error_code ) {
                $attributes['errors'][]= $this->getErrorMessage( $error_code );
            }
        }

        return LW_Template::getTemplate( 'client_templates','password_lost_form', $attributes, false);
    }


    public function doPasswordLost() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            $errors = retrieve_password();
            if ( is_wp_error( $errors ) ) {
                // Errors found
                $redirect_url = home_url( 'lwu-lostpassword' );
                $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
            } else {
                // Email sent
                $redirect_url = home_url( 'lwu-login' );
                $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
            }

            wp_redirect( $redirect_url );
            exit;
        }
    }

    public function replaceRetrievePasswordMessage($message, $key, $user_login, $user_data){
        // Create new message
        $msg  = __( 'Hello!', 'lummi-wile-se' ) . "\r\n\r\n";
        $msg .= sprintf( __( 'You asked us to reset your password for your account using the email address %s.', 'lummi-wile-se' ), $user_login ) . "\r\n\r\n";
        $msg .= __( "If this was a mistake, or you didn't ask for a password reset, just ignore this email and nothing will happen.", 'lummi-wile-se' ) . "\r\n\r\n";
        $msg .= __( 'To reset your password, visit the following address:', 'lummi-wile-se' ) . "\r\n\r\n";
        $msg .= home_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n\r\n";
        $msg .= __( 'Thanks!', 'lummi-wile-se' ) . "\r\n";

        return $msg;
    }

    /**
     * Redirecting the user to the form to enter a new password.
     */
    public function redirectToPasswordReset() {
        if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {

            $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
            if ( ! $user || is_wp_error( $user ) ) {
                if ( $user && $user->get_error_code() === 'expired_key' ) {
                    wp_redirect( home_url( 'lwu-login?login=expiredkey' ) );
                } else {
                    wp_redirect( home_url( 'lwu-login?login=invalidkey' ) );
                }
                exit;
            }

            $redirect_url = home_url( 'lwu-resetpassword' );
            $redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
            $redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );

            wp_redirect( $redirect_url );
            exit;
        }
    }

    /**
     * Adding template and application attributes.
     *
     * @param $attributes
     * @param null $content
     * @return string|void
     */
    public function renderPasswordResetForm( $attributes, $content = null ) {
        $default_attributes = array( 'show_title' => false );
        $attributes = shortcode_atts( $default_attributes, $attributes );

        if ( is_user_logged_in() ) {
            return __( 'You are already signed in.', 'lummi-wile-se' );
        } else {
            if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
                $attributes['login'] = $_REQUEST['login'];
                $attributes['key'] = $_REQUEST['key'];

                // Error messages
                $errors = array();
                if ( isset( $_REQUEST['error'] ) ) {
                    $error_codes = explode( ',', $_REQUEST['error'] );

                    foreach ( $error_codes as $code ) {
                        $errors[]= $this->getErrorMessage( $code );
                    }
                }
                $attributes['errors'] = $errors;

                return LW_Template::getTemplate( 'client_templates','password_reset_form', $attributes, false);
            } else {
                return __( 'Invalid password reset link.', 'lummi-wile-se' );
            }
        }
    }

    /**
     * Do password reset
     */
    public function doPasswordReset() {
        if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
            $rp_key = $_REQUEST['rp_key'];
            $rp_login = $_REQUEST['rp_login'];

            $user = check_password_reset_key( $rp_key, $rp_login );

            if ( ! $user || is_wp_error( $user ) ) {
                if ( $user && $user->get_error_code() === 'expired_key' ) {
                    wp_redirect( home_url( 'lwu-login?login=expiredkey' ) );
                } else {
                    wp_redirect( home_url( 'lwu-login?login=invalidkey' ) );
                }
                exit;
            }

            if ( isset( $_POST['pass1'] ) ) {
                if ( $_POST['pass1'] != $_POST['pass2'] ) {
                    // Passwords don't match
                    $redirect_url = home_url( 'lwu-resetpassword' );
                    $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                    $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                    $redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );

                    wp_redirect( $redirect_url );
                    exit;
                }

                if ( empty( $_POST['pass1'] ) ) {
                    // Password is empty
                    $redirect_url = home_url( 'lwu-resetpassword' );

                    $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                    $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                    $redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );

                    wp_redirect( $redirect_url );
                    exit;
                }

                // Parameter checks OK, reset password
                reset_password( $user, $_POST['pass1'] );
                wp_redirect( home_url( 'lwu-login?password=changed' ) );
            } else {
                echo "Invalid request.";
            }
            exit;
        }
    }

}