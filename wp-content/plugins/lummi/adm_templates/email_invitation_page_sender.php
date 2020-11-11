<div class="wrap">
<h1>Email Sender</h1>
<table id="email_invitation_table">
    <tr>
        <td class="email_invitation_header" style="border-bottom: 1px solid #ccc; padding: 10px 5px">
            <div class="field_control" style="float: right;">
                <label for="clubs_selector">Choice Club</label><br/>
                <select id="clubs_selector">
                    <option value="all">All Users</option>
                    <?php
                        if(count($attributes["available_clubs"]) > 0):
                        foreach ($attributes["available_clubs"] as $key_club => $value):
                     ?>
                    <option value="<?php echo $key_club; ?>"><?php echo $value; ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <form method="POST" action="<?php //echo $_SERVER['PHP_SELF'] . '?page=manage-club-email'; ?>" class="lumm_email_form" id="lummi_email_form_id">
                <input type="hidden" name="choice_club" value="all"/>
                <input type="hidden" name="choice_club_slug" value="all"/>
                <div class="form-field sender_fields">
                    <div class="email_from_area">
                        <label for="email_from"><?php _e('From','lummi-wild-se'); ?></label>
                        <input id="email_from" type="text" name="email_from" value="<?php if(get_option('email_invitation_from')){echo esc_attr(get_option('email_invitation_from'));}?>"/>
<!--                        <button id="email_address_template" class="btn_event dashicons dashicons-media-text"></button>-->
                    </div>
                    <div class="email_from_area">
                        <label for="email_from_name"><?php _e('Name','lummi-wild-se'); ?></label>
                        <input id="email_from_name" type="text" name="email_from_name" value="<?php if(get_option('email_invitation_name')){echo esc_attr(get_option('email_invitation_name'));}?>"/>
                        <!--                        <button id="email_address_template" class="btn_event dashicons dashicons-media-text"></button>-->
                    </div>
                    <div class="subject_fields">
                        <label for="email_subject"><?php _e('Subject','lummi-wild-se'); ?></label>
                        <input id="email_subject" type="text" name="email_subject" value="<?php if(get_option('email_invitation_subject')){echo esc_attr(get_option('email_invitation_subject'));}?>"/>
                        <!--                        <button id="email_subject_template" class="btn_event dashicons dashicons-media-text"></button>-->
                    </div>
                    <div class="subject_fields">
                        <label for="email_subject"><?php _e('Other Email','lummi-wild-se'); ?></label>
                        <input id="other_email" type="text" name="other_email" /><br/>
                        <small>Email to the user who is not listed</small>
                    </div>
                </div>
                <div class="form-field email_club_users">
                    <label for="newmember_member"><?php _e('Contact List','lummi-wild-se'); ?></label>
                    <select id="lummi_email_sender" multiple name="clients_addresses[]" style="width: 30%;">
                        <?php
                        foreach ($attributes['all_users'] as $user_value): ?>
                            <option value="<?php echo $user_value->data->user_email; ?>"><?php echo $user_value->data->user_email.' - '.$user_value->data->display_name; ?></option>
                            <?php
                        endforeach; ?>
                    </select>
                    <a id="select-all" href="#">Select All</a>
                    <a id="deselect-all" href="#">Deselect All</a>
                </div>
	            <?php if( ! empty($attributes["all_users"]) ) : ?>
                    <div class="exporter" style="float: right;">
                        <input type="submit" name="exporter" value="Export Emails" />
                        <label for="comma_sep">Comma Separated</label>
                        <input id="comma_sep" type="radio" name="type_export" value="comma" checked="checked"/>
                        <label for="per_line">One Per Line</label>
                        <input id="per_line" type="radio" name="type_export" value="per_line" />
                    </div>
	            <?php endif; ?>
                <div class="email_editor_field">
                    <?php

                    $settings = array(
                        'media_buttons' => true
                    );
                    $content = get_option('email_invitation_message')? stripslashes(get_option('email_invitation_message')) : null;
                    wp_editor($content,'invitation_message_submit',$settings);
                    ?>
                    <?php submit_button('Send','primary','send_email_invitation'); ?>
                </div>
            </form>
        </td>
    </tr>
</table>
</div>