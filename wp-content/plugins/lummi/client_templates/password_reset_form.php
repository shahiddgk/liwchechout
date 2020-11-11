<div id="lw-password-reset-form" class="lw-password-reset-form">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Pick a New Password', 'lummi-wile-se' ); ?></h3>
    <?php endif; ?>

    <form name="resetpassform" id="resetpassform" action="<?php echo home_url( 'wp-login.php?action=resetpass&wpe-login=liw' ); ?><?php echo home_url( 'wp-login.php?action=resetpass&wpe-login=liw' ); ?>" method="post" autocomplete="off">
        <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr( $attributes['login'] ); ?>" autocomplete="off" />
        <input type="hidden" name="rp_key" value="<?php echo esc_attr( $attributes['key'] ); ?>" />
        <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
            <?php foreach ( $attributes['errors'] as $error ) : ?>
                <p>
                    <?php echo $error; ?>
                </p>
            <?php endforeach; ?>
        <?php endif; ?>
        <p>
            <label for="pass1"><?php _e( 'New password', 'lummi-wile-se' ) ?></label>
            <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
        </p>
        <p>
            <label for="pass2"><?php _e( 'Repeat new password', 'lummi-wile-se' ) ?></label>
            <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
        </p>
        <p class="description"><?php echo wp_get_password_hint(); ?></p>
        <p class="resetpass-submit">
            <input class="button" type="submit" name="submit" id="resetpass-button" value="<?php _e( 'Reset Password', 'lummi-wile-se' ); ?>" />
        </p>
    </form>
</div>