/*
 * Customizer Scripts
 */

jQuery(document).ready(function() {

    /**
     * Change description
     */
    jQuery('#customize-info .customize-panel-description').html(rp_decorator.labels.description);

    // Add reset button
    jQuery('#customize-header-actions input#save').after('<input type="submit" name="rp_decorator_reset" id="rp_decorator_reset" class="button button-secondary" value="' + rp_decorator.labels.reset + '" style="float: right; margin-right: 8px; margin-left: 9px;">');

    // Handle reset button click
    jQuery('#customize-header-actions #rp_decorator_reset').click(function(e) {

        // Prevent form submit
        e.preventDefault();

        // Display confirmation prompt
        var confirmation = confirm(rp_decorator.labels.reset_confirmation);

        // Check user input
        if (!confirmation) {
            return;
        }

        // Disable reset button
        jQuery(this).prop('disabled', true);

        // Populate request data object
        var data = {
            wp_customize:   'on',
            action:         'rp_decorator_reset',
        };

        // Send request to server
        jQuery.post(rp_decorator.ajax_url, data, function() {
            wp.customize.state('saved').set(true);
            window.location.replace(rp_decorator.customizer_url);
        });
    });

});
