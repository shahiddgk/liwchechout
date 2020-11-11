<div class="wrap" id="lummi_email_container">
    <h1><?php echo $attributes['title'] ?></h1>
    <p>This is a list of your club members’ email addresses. You can copy and paste them into your favorite email program.<br/>
        <small>Note: it is considered best practice to BCC when sending to a large group.</small></p>
    <div class="lumm_email_form" id="lummi_email_form_id">
        <div class="form-field email_club_users">
            <form method="POST">
                <div style="float: left;">
                    <label for="all-emails" style="display: block; font-size: 16px; font-weight: 600; padding-bottom: 5px;">All Club Email Addresses</label>
	                <?php
                        $i = 0;
                        $len = count($attributes['all_users']);
                        $sep = ','."\r\n";
	                ?>
                    <textarea rows="10" cols="60" id="all-emails-orders" style="float: left;" readonly><?php foreach ($attributes['all_users'] as $user_value){if ($i == $len - 1) {$sep = '';}?><?php echo $user_value['user_email'].$sep; $i++;}; ?></textarea>
                    <input type="submit" name="ae_csv" value="Download CSV" class="button button-primary" style="margin-top: 10px;"/>
                </div>
            </form>
            <form method="POST">
                <div style="float: left;">
                    <label for="all-emails-orders" style="display: block; font-size: 16px; font-weight: 600; padding-bottom: 5px;"">Current Order Email Addresses</label>
                    <?php
                        $i = 0;
                        $len = count($attributes['ordered_users']);
                        $sep = ','."\r\n";
                    ?>
                    <textarea rows="10" cols="60" id="all-emails-orders" style="float: left;" readonly><?php foreach ($attributes['ordered_users'] as $ord_uval){if ($i == $len - 1) {$sep = '';}?><?php echo $ord_uval['user_email'].$sep; $i++;}; ?></textarea>
                    <input type="submit" name="aeo_csv" value="Download CSV" class="button button-primary" style="margin-top: 10px;"/>
                </div>
            </form>
        </div>
    </div>
</div>