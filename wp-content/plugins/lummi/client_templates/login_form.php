<?php if ( count( $attributes['errors'] ) > 0 ) : ?>
    <?php foreach ( $attributes['errors'] as $error ) : ?>
        <p class="login-error">
            <?php echo $error; ?>
        </p>
    <?php endforeach; ?>
<?php endif; ?>
<?php if ( $attributes['logged_out'] ) : ?>
    <p class="login-info">
        <?php _e( 'You have signed out. Would you like to sign in again?', 'lummi-wild-se' ); ?>
    </p>
<?php endif; ?>
<?php if ( $attributes['registered'] ) : ?>
    <p class="login-info">
        <?php
        printf(
            __( 'You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'lummi-wild-se' ),
            get_bloginfo( 'name' )
        );
        ?>
    </p>
<?php endif; ?>
<?php if ( $attributes['lost_password_sent'] ) : ?>
    <p class="login-info">
        <?php _e( 'Check your email for a link to reset your password.', 'lummi-wild-se' ); ?>
    </p>
<?php endif; ?>
<?php if ( $attributes['password_updated'] ) : ?>
    <p class="login-info">
        <?php _e( 'Your password has been changed. You can sign in now.', 'lummi-wild-se' ); ?>
    </p>
<?php endif; ?>
<div class="lw-login-form-container">
    <form method="post" action="<?php echo wp_login_url("?wpe-login=liw"); ?>">
        <p class="login-username">
            <label for="user_login"><?php _e( 'Username or Email', 'lummi-wild-se' ); ?></label>
            <input type="text" name="log" id="user_login">
        </p>
        <p class="login-password">
            <label for="user_pass"><?php _e( 'Password', 'lummi-wild-se' ); ?></label>
            <input type="password" name="pwd" id="user_pass">
        </p>
        <p class="login-submit" style="padding-bottom:0;">
            <input type="submit" value="<?php _e( 'Sign In', 'lummi-wild-se' ); ?>">
        </p>
		<?php if ($_GET['login'] != 'incorrect_password') { ?>
			<div class="forgotten-pw">
				<a href="<?php bloginfo('url'); ?>/lwu-lostpassword/">Forgot your password?</a>
			</div>
		<?php } ?>
    </form>
</div>
<?php
    if($_COOKIE['club_cook']){
        unset($_COOKIE['club_cook']);
    }
?>