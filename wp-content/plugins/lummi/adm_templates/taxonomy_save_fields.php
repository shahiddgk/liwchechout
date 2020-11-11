<?php
    $args = array(
            'role' => 'club-manager',
            'fields' => array('ID','display_name'),
            );
    $managers = get_users($args);
?>
<div class="form-field term-group">
    <label for="club_manager"><?php _e('Club Manager','lummi-wild-se'); ?></label>
    <select class="postform" id="equipment-group" name="clubs_manager_id">
            <option value="none"><?php _e('None','lummi-wild-se'); ?></option>
        <?php foreach ($managers as $manager) : ?>
            <option value="<?php echo $manager->ID; ?>"><?php echo $manager->display_name; ?></option>
        <?php endforeach; ?>
    </select>
    <label for="club_discount_p"><?php _e('Discount (%)','lummi-wild-se'); ?></label>
    <input type="number" name="clubs_discount_p" step="0.01" min="0" max="100"/>
    <div class="form-field">
        <div class="club-date-container">
            <label class="clubs_open_date" for="clubs_open_date"><?php _e('Open Order Date','lummi-wild-se'); ?></label>
            <input id="clubs_open_date" type="text" name="clubs_open_date" />
        </div>
        <div class="club-date-container">
            <label class="clubs_close_date" for="clubs_close_date"><?php _e('Close Order Date','lummi-wild-se'); ?></label>
            <input id="clubs_close_date" type="text" name="clubs_close_date" />
        </div>
    </div>
    <label for="clubs_ship_date"><?php _e('Shipping Date','lummi-wild-se'); ?></label>
    <input id="clubs_ship_date" type="text" name="clubs_ship_date" />

    <label for="clubs_arrival_date"><?php _e('Arrival Date','lummi-wild-se'); ?></label>
    <input id="clubs_arrival_date" type="text" name="clubs_arrival_date" />

    <label for="clubs_notes"><?php _e('Notes','lummi-wild-se'); ?></label>
    <textarea id="clubs_notes" name="clubs_notes"></textarea>

    <label for="clubs_ship_price"><?php _e('Shipping Price (per pound)','lummi-wild-se'); ?></label>
    <input id="clubs_ship_price" type="text" name="clubs_ship_price" />

    <label for="club_address"><?php _e('Club Address','lummi-wild-se'); ?></label>
    <input type="text" name="clubs_address" />

    <label for="clubs_phone"><?php _e('Business Phone','lummi-wild-se'); ?></label>
    <input type="text" name="clubs_phone" value=""/>

    <label for="clubs_email"><?php _e('Work email','lummi-wild-se'); ?></label>
    <input type="email" name="clubs_email" value="" />
    <div class="club_save_field_editor">
        <label for="clubs_after_login_massage"><?php _e('Login Message','lummi-wild-se'); ?></label>
        <?php
        $login_massage_settings = array( 'media_buttons' => true,'textarea_rows' => 1);
        wp_editor('','clubs_after_login_massage',$login_massage_settings);
        ?>
    </div>
    <div class="club_save_field_editor">
        <label for="clubs_inactive_massage"><?php _e('Inactive Message','lummi-wild-se'); ?></label>
        <?php
        $inactive_massage_settings = array( 'media_buttons' => true,'textarea_rows' => 1 );
        wp_editor('','clubs_inactive_massage',$inactive_massage_settings);
        ?>
    </div>
    <div class="club_save_field_editor">
        <label for="clubs_checkout_massage"><?php _e('Checkout Message','lummi-wild-se'); ?></label>
        <?php
        $checkout_massage_settings = array( 'media_buttons' => true,'textarea_rows' => 1 );
        wp_editor('','clubs_checkout_massage',$checkout_massage_settings);
        ?>
    </div>
    <div class="club_save_field_editor">
        <label for="clubs_confirmation_massage"><?php _e('Order Confirmation message','lummi-wild-se'); ?></label>
        <?php
        $confirmation_massage_settings = array( 'media_buttons' => true,'textarea_rows' => 1);
        wp_editor('','clubs_confirmation_massage',$confirmation_massage_settings);
        ?>
    </div>
    <div class="club_save_field_editor">
        <label for="clubs_logo"><?php _e('Club Logo','lummi-wild-se'); ?></label>
        <?php
        $logo_settings = array(
            'media_buttons' => true,
            'textarea_rows' => 1,
            'quicktags'     => array("buttons"=>"link,img,close"),
            'tinymce'       => true,
        );
        $logo = $attributes['clubs_logo'];
        wp_editor($logo,'clubs_logo',$logo_settings);
        ?>
    </div>
    <label for="club_active"><?php _e('Active','lummi-wild-se'); ?></label>
    <input type="radio" name="clubs_active" value="on"/> Enable<br/>
    <input type="radio" name="clubs_active" value="off"/> Disable<br/>

    <label class="set-default-club"><?php _e('Is this club by default?','lummi-wild-se'); ?></label>
    <select name="default_club">
        <option value="">No</option>
        <option value="default_club">Yes</option>
    </select>
</div>