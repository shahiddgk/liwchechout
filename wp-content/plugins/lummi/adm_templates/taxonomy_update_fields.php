<?php

global $tag_ID, $wpdb;
$status = 0;

$periods = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}clubs_history WHERE create_date IS NOT NULL AND club_id=$tag_ID LIMIT 1");
$current_id = $wpdb->get_results("SELECT MAX(id) as last_id FROM {$wpdb->prefix}clubs_history WHERE club_id=$tag_ID LIMIT 1");

if( $attributes['clubs_open_date'] && $attributes['clubs_close_date']) :
?>
<tr>
    <th>
        <label class="packing_slip_club"><?php _e('Generate Packing Slip','lummi-wild-se'); ?></label>
    </th>
    <form style="display: none;"></form>
    <td>
        <form method="POST" target="_blank">
            <?php submit_button( 'Packing Slip', 'primary', 'packing_slip' ); ?>
            <input type="hidden" value="<?php echo $tag_ID; ?>" name="admin_tag_id" />
        </form>
    </td>
</tr>
<tr>
    <th>
        <label class="packing_slip_club"><?php _e('Order Details','lummi-wild-se'); ?></label>
    </th>
    <td>
        <form method="POST" target="_blank">
            <?php submit_button( 'Order Details', 'primary', 'invoices_list' ); ?>
            <input type="hidden" value="<?php echo $tag_ID; ?>" name="admin_tag_id" />
        </form>
    </td>
</tr>
<?php endif; ?>
<tr class="form-field term-group-wrap">
        <th scope="row">
            <label for="club-managers"><?php _e( 'Current manager:', 'lummi-wild-se' ); ?></label>
        </th>
        <td>
            <select class="club-managers" id="club-managers" name="clubs_manager_id">
                <option value=""><?php _e( 'none', 'lummi-wild-se' ); ?></option>
                <?php foreach( $attributes['all_managers'] as $m_key => $m_value ) : ?>
                    <option value="<?php echo esc_attr($m_key); ?>" <?php selected( $m_key, $attributes['clubs_manager_id'] ); ?>><?php echo esc_attr($m_value); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr class="form-field club-discount-field">
        <th scope="row">
            <label for="club_discount_p"><?php _e('Discount (%)','lummi-wild-se'); ?></label>
        </th>
        <td>
            <input type="number" name="clubs_discount_p" step="0.01" min="0" max="100" value="<?php echo esc_attr($attributes['clubs_discount_p']); ?>"/>
        </td>
    </tr>
    <tr class="form-field club-chef-field">
        <th scope="row">
            <label for="chefs_club_table">Chef's Club Option</label>
        </th>
        <td>
            <table id="chefs_club_table" style="border: 1px solid #ccc; border-radius: 4px;">
                <tr>
                    <td style="width: 162px;">
                        <p style="padding-bottom: 2px;">Activate chef's club</p>
                        <div class="field">
	                        <?php $current_active = ( $attributes["chefs_active"] ) ? $attributes["chefs_active"] : 0 ; ?>
                            <input type="hidden" name="club_id" value="<?php echo $tag_ID; ?>"/>
                            <input type="hidden" name="current_active" value="<?php echo $current_active; ?>"/>
                            <div class="switch-field">
                                <?php
                                    $check = ( $attributes["chefs_active"] ) ? 'checked="checked"' : null;
                                    if( ! $check ){
                                        $def_check = 'checked="checked"';
                                    }
                                ?>
                                <input type="radio" id="switch_right" name="chefs_active" value="0" <?php echo $check; echo $def_check; ?> />
                                <label for="switch_right" class="inactive">Inactive</label>
                                <input type="radio" id="switch_left" name="chefs_active" value="1" <?php echo $check; ?>/>
                                <label for="switch_left" class="active">Active</label>
                            </div>
                        </div>
                    </td>
                    <td style="width: 200px;">
                        <p style="padding-bottom: 2px;">Percent value</p>
                        <input type="number" step="0.01" min="0" max="100" name="chefs_prc" value="<?php echo esc_attr($attributes['chefs_prc']); ?>" data-old-value="<?php echo esc_attr($attributes['chefs_prc']); ?>"/>
                    </td>
                    <td class="chefs-button-container">
                        <button type="button" id="apply_chefs" class="button button-primary" disabled="disabled">Apply Changes</button>
                        <div class="chef-loader-image"></div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <div class="info-chefs">
                            <small class="info-box">
                                After activation, this club will be treated as a chefs club.<br/>
                                The standard percent will be summed with the chefs percent.<br/>
                                Initially, the percentage will be the same for all products, then it can be changed individually for each product.
                            </small>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="form-field">
        <th>
            <label class="clubs_date_container" for="clubs_date_container"><?php _e('Dates','lummi-wild-se'); ?></label>
        </th>
        <td>
            <input type="hidden" name="current_id_period" value="<?php echo $current_id[0]->last_id; ?>"/>
            <div class="club-date-container">
                <label class="clubs_open_date" for="clubs_open_date"><?php _e('Open Order Date','lummi-wild-se'); ?></label>
                <input type="hidden" name="hidden_date_open" value="<?php echo esc_attr($attributes['clubs_open_date']); ?>" />
                <input id="clubs_open_date" type="text" name="clubs_open_date" value="<?php echo esc_attr($attributes['clubs_open_date']); ?>"/>
            </div>
            <div class="club-date-container">
                <label class="clubs_close_date" for="clubs_close_date"><?php _e('Close Order Date','lummi-wild-se'); ?></label>
                <input type="hidden" name="hidden_date_close" value="<?php echo esc_attr($attributes['clubs_close_date']); ?>" />
                <input id="clubs_close_date" type="text" name="clubs_close_date" value="<?php echo esc_attr($attributes['clubs_close_date']); ?>"/>
            </div>
            <?php if( empty( $periods ) && ! empty($attributes['clubs_open_date']) && ! empty($attributes['clubs_close_date'])) : ?>
            <div class="unsaved-period" style="display: table; position: relative; top: 20px;">
                <div class="lo"></div>
                <span class="rem">You have a valid club period, but it is not saved!</span><br/><br/>
                <span class="rem"><i>Once you click on the "Save it now" button,<br/> this message will be hidden forever.</i></span>
                <input type="button" name="force-period" value="Save it now" class="button button-primary rem" style="margin-left: 88px; position: absolute; top: 5px;"/>
            </div>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th>
            <label for="clubs_shipping_date"><?php _e('Shipping Date','lummi-wild-se'); ?></label>
        </th>
        <td>
            <input id="clubs_ship_date" type="text" name="clubs_ship_date" value="<?php echo esc_attr($attributes['clubs_ship_date']); ?>"/>
        </td>
    </tr>
    <tr>
        <th>
            <label for="clubs_arrival_date"><?php _e('Arrival Date','lummi-wild-se'); ?></label>
        </th>
        <td>
            <input id="clubs_arrival_date" type="text" name="clubs_arrival_date" value="<?php echo esc_attr($attributes['clubs_arrival_date']); ?>"/>
        </td>
    </tr>
    <tr>
        <th>
            <label for="clubs_notes"><?php _e('Notes','lummi-wild-se'); ?></label>
        </th>
        <td>
            <textarea style="width: 550px; height: 100px;" id="clubs_notes" name="clubs_notes"><?php echo esc_attr($attributes['clubs_notes']); ?></textarea>
        </td>
    </tr>
    <tr>
        <th>
            <label for="clubs_ship_price"><?php _e('Shipping Price','lummi-wild-se'); ?></label>
        </th>
        <td>
            <input id="clubs_ship_price" type="text" name="clubs_ship_price" value="<?php echo esc_attr($attributes['clubs_ship_price']); ?>"/>
        </td>
    </tr>
    <tr class="form-field club-address-field">
        <th scope="row">
            <label for="club_address"><?php _e('Club Address','lummi-wild-se'); ?></label>
        </th>
        <td>
            <input type="text" name="clubs_address" value="<?php echo esc_attr($attributes['clubs_address']); ?>"/>
        </td>
    </tr>
   <tr class="form-field club-phone-field">
       <th scope="row">
           <label for="clubs_phone"><?php _e('Business Phone','lummi-wild-se'); ?></label>
       </th>
       <td>
           <input type="text" name="clubs_phone" value="<?php echo esc_attr($attributes['clubs_phone']); ?>" />
       </td>
   </tr>
   <tr class="form-field club-email-field">
       <th scope="row">
           <label for="clubs_email"><?php _e('Work email','lummi-wild-se'); ?></label>
       </th>
       <td>
           <input type="email" name="clubs_email" value="<?php echo esc_attr($attributes['clubs_email']); ?>" />
       </td>
   </tr>
    <tr class="form-field club-email-field">
        <th scope="row">
            <label for="clubs_after_login_massage"><?php _e('Login Message','lummi-wild-se'); ?></label>
        </th>
        <td>
            <?php
            $login_massage_settings = array( 'media_buttons' => true,'textarea_rows' => 1 );
            $login_massage_content = $attributes['clubs_after_login_massage'];
            wp_editor($login_massage_content,'clubs_after_login_massage',$login_massage_settings);
            ?>
        </td>
    </tr>
    <tr class="form-field club-inactive-field">
        <th scope="row">
            <label for="clubs_inactive_massage"><?php _e('Club Inactive Message','lummi-wild-se'); ?></label>
        </th>
        <td>
            <?php
            $inactive_massage_settings = array( 'media_buttons' => true,'textarea_rows' => 1 );
            $inactive_massage_content = $attributes['clubs_inactive_massage'];
            wp_editor($inactive_massage_content,'clubs_inactive_massage',$inactive_massage_settings);
            ?>
        </td>
    </tr>
    <tr class="form-field club-checkout-field">
        <th scope="row">
            <label for="clubs_checkout_massage"><?php _e('Checkout message','lummi-wild-se'); ?></label>
        </th>
        <td>
            <?php
            $checkout_massage_settings = array( 'media_buttons' => true,'textarea_rows' => 1 );
            $checkout_massage_content = $attributes['clubs_checkout_massage'];
            wp_editor($checkout_massage_content,'clubs_checkout_massage',$checkout_massage_settings);
            ?>
        </td>
    </tr>
    <tr class="form-field club-confirmation-field">
        <th scope="row">
            <label for="clubs_confirmation_massage"><?php _e('Order Confirmation Message','lummi-wild-se'); ?></label>
        </th>
        <td>
            <?php
            $confirmation_massage_settings = array( 'media_buttons' => true,'textarea_rows' => 1 );
            $confirmation_massage_content = $attributes['clubs_confirmation_massage'];
            wp_editor($confirmation_massage_content,'clubs_confirmation_massage',$confirmation_massage_settings);
            ?>
        </td>
    </tr>
    <tr class="form-field club-logo-field">
        <th scope="row">
            <label for="clubs_logo"><?php _e('Club Logo','lummi-wild-se'); ?></label>
        </th>
        <td>
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
        </td>
    </tr>
    <tr class="form-field club-invitation_link-field">
        <th>
            <label for="invitation_link">Invitation for this club</label>
        </th>
        <td>
            <input type="hidden" name="hid-invitation_link" value="<?php echo esc_attr($attributes['invitation_link']); ?>"/>
            <input type="text" name="invitation_link" value="<?php echo esc_attr($attributes['invitation_link']); ?>">
        </td>
    </tr>
    <tr class="form-field club-active-field">
        <th scope="row">
            <label for="club_active"><?php _e('Activate','lummi-wild-se'); ?></label>
        </th>
        <td>
            <input type="radio" name="clubs_active" value="on" <?php checked($attributes['clubs_active'],'on'); ?>/> Enable<br/>
            <input type="radio" name="clubs_active" value="off" <?php checked($attributes['clubs_active'],'off'); ?>/> Disable
        </td>
    </tr>
    <tr>
        <th>
            <label class="set-default-club"><?php _e('Is this club by default?','lummi-wild-se'); ?></label>
        </th>
        <td>
            <select name="default_club">
                <option value="">No</option>
                <option value="default_club" <?php selected( $attributes['default_club'],'default_club',true); ?>>Yes</option>
            </select>
        </td>
    </tr>