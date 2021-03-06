<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WgactGtag {

	public static $conversion_id;

	public static function set_conversion_id ($conversion_id) {
		self::$conversion_id = $conversion_id;
	}

	public static function inject () {
		?>

		<!--noptimize-->
		<!-- Global site tag (gtag.js) - Google Ads: <?php echo esc_html( self::$conversion_id ) ?> -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=AW-<?php echo esc_html( self::$conversion_id ) ?>"></script>
		<script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());

            gtag('config', 'AW-<?php echo esc_html( self::$conversion_id ) ?>');
		</script>
		<!--/noptimize-->

		<?php

	}
}