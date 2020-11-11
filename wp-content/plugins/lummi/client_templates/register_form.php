<div id="lw-register-form" class="lw-register-form">
    <?php if ( $attributes['show_title'] ) : ?>
        <h3><?php _e( 'Register', 'lummi-wild-se' ); ?></h3>
    <?php endif; ?>
    <?php if ( count( $attributes['errors'] ) > 0 ) : ?>
        <?php foreach ( $attributes['errors'] as $error ) : ?>
            <p>
                <?php echo $error; ?>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>
    <form id="signupform" action="<?php echo site_url("wp-login.php?action=register&wpe-login=liw"); ?>" method="POST">
        <p class="form-row">
            <label for="email"><?php _e( 'Username', 'lummi-wild-se' ); ?> <strong>*</strong></label>
            <input type="text" name="user_login" id="user-login" />
        </p>
        <p class="form-row">
            <label for="email"><?php _e( 'Email', 'lummi-wild-se' ); ?> <strong>*</strong></label>
            <input type="text" name="email" id="email" />
        </p>
        <p class="form-row">
            <label for="first_name"><?php _e( 'First name', 'lummi-wild-se' ); ?><strong>*</strong></label>
            <input type="text" name="first_name" id="first-name" />
        </p>
        <p class="form-row">
            <label for="last_name"><?php _e( 'Last name', 'lummi-wild-se' ); ?><strong>*</strong></label>
            <input type="text" name="last_name" id="last-name" />
        </p>
        <?php if($attributes['clubs_select_menu'] != true) : ?>
        <p>
            <label for="user_club">Membership Club</label><br />
            <select name="user_club">
                <?php foreach ($attributes['clubs'] as $club_id => $club_name):
                    $selected = ($club_name == 'no membership') ? 'selected' : null;
                    ?>
                    <option value="<?php echo $club_id; ?>" <?php echo $selected; ?>><?php echo $club_name; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php else: ?>
            <input type="hidden" name="user_club" value="<?php echo $attributes["clubs"]["term_id"]; ?>" />
        <?php endif; ?>
        <?php if ( $attributes['recaptcha_activate'] ) : ?>
        <div class="recaptcha-container">
            <div class="g-recaptcha" data-sitekey="<?php echo $attributes['recaptcha_site_key']; ?>"></div>
        </div>
        <?php endif; ?>
        <p class="form-row">
            <?php _e( 'Note: Your password will be generated automatically and sent to your email address.', 'lummi-wild-se' ); ?>
        </p>
        <p class="signup-submit">
            <input type="submit" name="submit" class="register-button" value="<?php _e( 'Register', 'lummi-wild-se' ); ?>"/>
        </p>
    </form>
</div>