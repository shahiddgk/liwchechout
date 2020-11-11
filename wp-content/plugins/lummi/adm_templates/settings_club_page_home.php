<div class="wrap" id="lummi-settings-club-home-page">
    <div class="settings-club-title" style="float: left; width: 50%;">
        <h1>Manage Club</h1>
        <h2>Welcome to <?php echo $attributes['wellcome']?></h2>
        <button type="button" class="button button-primary show-walkthrough" style="margin-bottom: 15px;">Video Walkthrough</button>
        <div class="video-walkthrough">
            <?php
                // Video embed URL
                $src = "https://screencast-o-matic.com/embed?sc=cFV3q2o3zu&v=5&controls=1&title=0&ff=1";
            ?>
            <iframe width=853 height=512 frameborder="0" scrolling="no" src="<?php echo $src; ?>" allowfullscreen="true"></iframe>
        </div>
    </div>
    <div class="club-info" style="width: 50%;">
        <table>
            <tr>
                <td>Manager:</td>
                <td><strong><?php echo $attributes['manager_data']; ?></strong></td>
            </tr>
            <tr>
                <td>Phone:</td>
                <td><strong><?php echo $attributes['club_meta']['clubs_phone']; ?></strong></td>
            </tr>
            <tr>
                <td>Email:</td>
                <td><strong><?php echo $attributes['club_meta']['clubs_email']; ?></strong></td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><strong><?php echo $attributes['club_meta']['clubs_address']; ?></strong></td>
            </tr>
            <tr>
                <td>Discount (%):</td>
                <td><strong><?php echo $attributes['club_meta']["clubs_discount_p"]; ?></strong></td>
            </tr>
            <tr>
                <?php $is_club_active = ($attributes['club_meta']["clubs_active"] == 'on' && $attributes['date_status'] !== 1 )? 'Active' : 'Inactive'; ?>
                <?php $active_color = ($attributes['club_meta']["clubs_active"] == 'on' && $attributes['date_status'] !== 1)? 'green' : 'red'; ?>
                <td>Club Status:</td>
                <td style="color: <?php echo $active_color; ?>;"><strong><?php echo $is_club_active; ?></strong></td>
            </tr>
        </table>
    </div><hr />
    <div id="lw-container-id" class="lw-container">
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '?page=manage-club-home'; ?>">
            <div class="form-field settings-club-address-field">
                <label for="club_address"><?php _e('Club Address: ','lummi-wild-se'); ?></label>
                <input type="text" name="clubs_address"
                       value="<?php echo esc_attr($attributes['club_meta']['clubs_address']); ?>"/>
            </div>
            <div class="form-field settings-club-address-field">
                <label for="club_email"><?php _e('Club Email: ','lummi-wild-se'); ?></label>
                <input type="text" name="clubs_email"
                       value="<?php echo esc_attr($attributes['club_meta']['clubs_email']); ?>"/>
            </div>
            <div class="form-field settings-club-phone-field">
                <label for="club_phone"><?php _e('Club Phone: ','lummi-wild-se'); ?></label>
                <input type="text" name="clubs_phone"
                       value="<?php echo esc_attr($attributes['club_meta']['clubs_phone']); ?>"/>
            </div>
            <div class="form-field settings-club-ship-price-field">
                <label for="clubs_ship_price"><?php _e('Shipping Price (per pound): ','lummi-wild-se'); ?></label>
                <input type="text" name="clubs_ship_price"
                       value="<?php echo esc_attr($attributes['club_meta']['clubs_ship_price']); ?>"/>
            </div>
            <div class="form-field club-login-field club_save_field_editor">
                <label for="clubs_after_login_massage"><?php _e('Login Message','lummi-wild-se'); ?></label>
                <?php
                $login_massage_settings = array( 'media_buttons' => false,'textarea_rows' => 1 );
                $login_massage_content = $attributes['club_meta']['clubs_after_login_massage'];
                wp_editor($login_massage_content,'clubs_after_login_massage',$login_massage_settings);
                ?>
            </div>
            <div class="form-field club-inactive-field club_save_field_editor">
                <label for="clubs_inactive_massage"><?php _e('Club Inactive Message','lummi-wild-se'); ?></label>
                <?php
                $inactive_massage_settings = array( 'media_buttons' => false,'textarea_rows' => 1 );
                $inactive_massage_content = $attributes['club_meta']['clubs_inactive_massage'];
                wp_editor($inactive_massage_content,'clubs_inactive_massage',$inactive_massage_settings);
                ?>
            </div>
            <div class="form-field club-checkout-field club_save_field_editor">
                <label for="clubs_checkout_massage"><?php _e('Checkout Message','lummi-wild-se'); ?></label>
                <?php
                $checkout_massage_settings = array( 'media_buttons' => false,'textarea_rows' => 1 );
                $checkout_massage_content = $attributes['club_meta']['clubs_checkout_massage'];
                wp_editor($checkout_massage_content,'clubs_checkout_massage',$checkout_massage_settings);
                ?>
            </div>
            <div class="form-field club-confirmation-field club_save_field_editor">
                <label for="clubs_confirmation_massage"><?php _e('Order Confirmation Message','lummi-wild-se'); ?></label>
                <?php
                $confirmation_massage_settings = array( 'media_buttons' => false,'textarea_rows' => 1 );
                $confirmation_massage_content = $attributes['club_meta']['clubs_confirmation_massage'];
                wp_editor($confirmation_massage_content,'clubs_confirmation_massage',$confirmation_massage_settings);
                ?>
            </div>
            <div class="form-field club-invitation-link-field">
                <label for="clubs_confirmation_massage"><?php _e('Invitation for this club','lummi-wild-se'); ?></label>
                <input type="text" value="<?php echo esc_attr($attributes['club_meta']['invitation_link']); ?>" readonly/>
            </div>
            <?php submit_button('','primary','save_club'); ?>
        </form>
        <form method="POST">
            <label id="club-members" for="wp-list-table"><?php _e('Club members','lummi-wild-se'); ?></label>
            <?php
                echo $attributes['display'];
            ?>
            <?php submit_button('Remove Users','primary','remove_user'); ?>
        </form>
    </div>
</div>