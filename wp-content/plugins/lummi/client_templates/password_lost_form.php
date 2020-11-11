<div id="lw-password-lost-form" class="lw-password-lost-form">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Forgot Your Password?', 'lummi-wild-se' ); ?></h3>
    <?php endif; ?>
    <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
        <?php foreach ( $attributes['errors'] as $error ) : ?>
            <p>
                <?php echo $error; ?>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>
    <p>
        <?php
        _e("Enter your email address and we'll send you a link you can use to pick a new password.", 'lummi-wild-se');
        ?>
    </p>
    <form id="lostpasswordform" action="<?php echo wp_lostpassword_url("wpe-login=liw"); ?>" method="post">
        <p class="form-row">
            <label for="user_login"><?php _e( 'Email', 'lummi-wild-se' ); ?></label>
            <input type="text" name="user_login" id="user_login">
        </p>
        <p class="lostpassword-submit">
            <input type="submit" name="submit" class="lostpassword-button" value="<?php _e( 'Reset Password', 'lummi-wild-se' ); ?>"/>
        </p>
    </form>
</div>