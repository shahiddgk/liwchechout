=== Plugin Name ===
Contributors: firimar
Tags: lightbox, images, popup, iframe, magnific-popup, video, map
Requires at least: 3.0.1
Tested up to: 4.6.1
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin to add the Magnific Popup lightbox script to wordpress site for single images, image galleries, video, maps, dialog popups and other.

== Description ==

This basic plugin adds the Magnific Popup lightbox script to wordpress site and works with single images, iframe links and dialogs.

All you need to do to use Magnific Popup on a link is to add specific css class to 'a' tag. CSS class names can be configured on settings page.

For images you can use alt attribute to add captions.

Thanks to Dmitry Semenov for the original script: http://dimsemenov.com/plugins/magnific-popup/

This plugin is only basic version and does not support all features of Magnific Popup yet.

Plugin provides following functionality:

* Single image popup.

Usage example html:
`<a href="http://site.com/full-size.jpg" class="mpopup"><img alt="" src="http://site.com/thumbnail.jpg" /></a>`

* Image gallery popup with navigation.

Usage example html:
`<div class="popup-gallery">
	<a href="http://site.com/full-size-1.jpg" title="title1"><img alt="" src="http://site.com/thumbnail-1.jpg" /></a>
	<a href="http://site.com/full-size-2.jpg" title="title2"><img alt="" src="http://site.com/thumbnail-2.jpg" /></a>
	<a href="http://site.com/full-size-3.jpg" title="title3"><img alt="" src="http://site.com/thumbnail-3.jpg" /></a>
	<a href="http://site.com/full-size-4.jpg" title="title4"><img alt="" src="http://site.com/thumbnail-4.jpg" /></a>
</div>`

* Iframe popup (can contain external page, youtube video, google map, etc.)

Usage example html:
`<a href="http://www.youtube.com/watch?v=b0FhD19zj4I" class="mpopup_iframe">Video</a>
<a href="https://maps.google.com/maps?q=221B+Baker+Street,+London,+United+Kingdom&hl=en&t=v&hnear=221B+Baker+St,+London+NW1+6XE,+United+Kingdom" class="mpopup_iframe">Google Map</a>`

* Dialog popup (pops up a div from the current page)

Usage example html:
`<a href="#dialog" class="mpopup_div">Dialog</div>
<div style="display:none">
    <div id="dialog">
        Test dialog content
    </div>
</div>`

== Installation ==

Use the automatic installer from within the WordPress admin, or:

1. Download the .zip file by clicking on the Download button on the right
2. Unzip the file
3. Upload the files to your plugins directory
4. Go to the Plugins page from within the WordPress administration
5. Click Activate for WP Magnific Popup
6. After activation a new WP Magnific Popup options menu will appear under Settings.
7. Visit the Settings page to adjust values as you need.

You can now start using the plugin.

== Screenshots ==

1. Single image popup
2. Image gallery popup
3. Youtube video popup
4. Google map popup