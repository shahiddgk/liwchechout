<div class="wrap">
    <h1>Email Settings</h1>
    <form method="POST">
        <table class="email_invitation_settings_table">
            <tr>
                <td>
                    <h3>From email</h3>
                    <small>Default email sender</small>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="email" class="email_invitation_from" name="email_invitation_from" value="<?php if(get_option('email_invitation_from')){echo esc_attr(get_option('email_invitation_from'));}?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h3>From Name</h3>
                    <small>Default name</small>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" class="email_invitation_name" name="email_invitation_name" value="<?php if(get_option('email_invitation_name')){echo esc_attr(get_option('email_invitation_name'));}?>">
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Subect</h3>
                    <small>Default subject</small>
                </td>
            </tr>
            <tr>
                <td>
                    <textarea class="email_invitation_subject" rows="4" cols="50" name="email_invitation_subject"><?php if(get_option('email_invitation_subject')){echo esc_attr(get_option('email_invitation_subject'));}?></textarea>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Message</h3>
                    <small>Default message</small>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $content = get_option('email_invitation_message')? stripslashes(get_option('email_invitation_message')) : null;
                    wp_editor($content,'email_invitation_message',array( 'media_buttons' => true,'textarea_rows' => 10 ));
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php submit_button('Update','primary','update_email_settings')?>
                </td>
            </tr>
        </table>
    </form>
</div>