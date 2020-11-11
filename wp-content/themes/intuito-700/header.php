
<!DOCTYPE html>
<html lang="en">
	<head>
		<style>.async-hide { opacity: 0 !important} </style>
<script>(function(a,s,y,n,c,h,i,d,e){s.className+=' '+y;h.start=1*new Date;
h.end=i=function(){s.className=s.className.replace(RegExp(' ?'+y),'')};
(a[n]=a[n]||[]).hide=h;setTimeout(function(){i();h.end=null},c);h.timeout=c;
})(window,document.documentElement,'async-hide','dataLayer',4000,
{'GTM-N9KLFHT':true});</script>
		<script src="https://www.googleoptimize.com/optimize.js?id=GTM-N9KLFHT"></script>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/manifest.json">
		<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#002e6d">
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<link rel="icon" href="<?php bloginfo('template_directory'); ?>/favicon.png" type="image/png" />
		<meta name="theme-color" content="#002e6d">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/v4-shims.css">
		
		<?php 
		
				// <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
				// <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/v4-shims.css">
		?>

		<title>
			<?php wp_title(); ?>
		</title>
		
		<!-- Script Library for email overlay -->
		<script src="<?php bloginfo('template_directory'); ?>/js/js.cookie.js"></script>
		
		<!-- Stylesheets -->
		<link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i" type='text/css' media="screen" />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800' rel='stylesheet' type='text/css'>
		<meta name="google-site-verification" content="UvmGioQCE_ugjEEZi1kZVr_Xm95OjRIIbdC4cp_IP_o" />
				
		<?php
			if (is_user_logged_in()) { 
				$logged_status = "logged-in"; ?>
			<?php } else { 
				$logged_status = "logged-out";
			?>
				<style>
					#menu-item-1581 { display:inline-block; }
				</style>
			<?php }
		?>

		<?php $user = wp_get_current_user(); //print_r($user);
			if ( in_array( 'club-manager', (array) $user->roles ) OR in_array( 'administrator', (array) $user->roles ) ) { ?>
				<style>
					#menu-item-1517 {display:inline-block;}
					/*.header { top:32px!important; }*/
					
					@media screen and (max-width: 782px) {
						#wprmenu_bar{top:46px!important;}
						.header { top:46px!important; }
					}
				</style>
		<?php } ?>
		
		<?php
			$user_club = get_user_meta($user->ID, 'user_club', 'true');
			$club_status = club_status($user_club);
			//styles for public users
			if ( ( $user_club == '37' ) OR ( $user_club == '' ) OR ( $club_status == 'closed' ) ) { ?>
				<style>
					.shipping-calculator-button {
						display: inline!important;
						color:#666!important; 
					}
					.club-name { display:none!important; }
				</style>
			<?php }	else { ?>
				<style>
					#ship-to-different-address, #order_comments_field { display:none; }
				</style>
			<?php } ?>
		
		<div style="display:none"><?php echo 'user club: '.$user_club; ?></div>
		
		<?php my_custom_wc_free_shipping_notice($user_club); ?>

		<?php
			if ( in_array( 'administrator', (array) $user->roles ) ) {
				$this_user = "administrator";
			} elseif (  in_array( 'club-manager', (array) $user->roles ) ) {
				$this_user = 'club-manager';
			} elseif (  in_array( 'club-member', (array) $user->roles ) ) {
				$this_user = 'club-member';
			} elseif (  in_array( 'customer', (array) $user->roles ) ) {
				$this_user = 'customer';
			} else { $this_user= 'public-user'; }
		?>

		<?php
			if (is_page(1499)) { ?>
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
				  var js, fjs = d.getElementsByTagName(s)[0];
				  if (d.getElementById(id)) return;
				  js = d.createElement(s); js.id = id;
				  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=192545589279";
				  fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));</script>
			<?php }
		?>
		
        <?php 
            $user = wp_get_current_user();
                if ( current_user_can('edit_posts') ) { ?>
                    <style>
                        button#responsive-menu-button { top: 70px!important; }
                    </style>
            <?php }
        ?>
		
		<?php the_field('header_scripts','option'); ?>
		
		<?php wp_head(); ?>
        
		<!-- Hotjar Tracking Code for lummiislandwild.com -->
		<script>
			(function(h,o,t,j,a,r){
				h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
				h._hjSettings={hjid:723574,hjsv:6};
				a=o.getElementsByTagName('head')[0];
				r=o.createElement('script');r.async=1;
				r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
				a.appendChild(r);
			})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
		</script>
		
		<!-- Facebook Pixel Code -->
		<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
		document,'script','https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '1876412529038424'); // Insert your pixel ID here.
		fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=1225439460911523&ev=PageView&noscript=1"
		/></noscript>
		<!-- DO NOT MODIFY -->
		<!-- End Facebook Pixel Code -->
		
		<?php 
			if(is_home() || is_front_page()) { 
			echo '<link rel="amphtml" href="https://lummiislandwild.com/amp" />';
		} ?>
		
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M4FNBTK"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->		
	</head>
	<body id="page-<?php global $post; the_ID(); ?>" <?php echo 'class="'.$this_user . ' '. $logged_status.'"'; ?> data-club-id="<?php echo $user_club; ?>" data-club-status="<?php echo club_status($club_id); ?>">

			<header class="header" id="header">
				<?php if ( get_field('covid-19_notice','option') == 'something' ) { ?>
				<style>
					.covid-notice { color:white; text-align:center; padding:10px; background:#8b6e4a; border-bottom:1px solid #002e6d; position:fixed; 
						top:0; left:0; right:0; z-index:9999; }
					.covid-notice h3 { display:inline; }
					.total-wrap { margin-top:230px; }
					.header { margin-top:45px; }
				</style>
				<div class="covid-notice">
					<h3>
						Covid-19 Notice
					</h3>
						Thanks to a dedicated core team at Lummi Island Wild we are staying healthy and currently shipping on time. 
						Special procedures are in place to ensure your order is handled with the utmost care
				</div>
				<?php } ?>
				
				<?php
					if (is_user_logged_in()) { ?>
						<style>
							@media screen and (max-width: 500px) {
								.header-top { top:46px; }
								button#responsive-menu-button { top:41px!important; }
								.total-wrap { margin-top:49px; }
								.header { padding-top:0; }
							}
							.header .club-container .login a { font-size:.7em; }
						</style>
					<?php } else { ?>
						<style>
							
						</style>
					<?php }
				?>
				
				
				<div class="header-top">
					<div class="header-top-content">
						<div class="club-container">
							<?php if ( ($this_user == 'club-member') OR ($this_user == 'club-manager') ) { ?>
							    <div class=club-name>
                                    <a href="https://lummiislandwild.com/our-seafood"><?php bloginfo('name'); ?></a>
                                </div>
                            <?php } ?>
                            <?php // if ( WC()->cart->get_cart_contents_count() != 0 ) {  bloginfo('url'); /cart ?>
                                <a href="javascript:void(0);" onclick="toggle_popup();" style="<?=(WC()->cart->get_cart_contents_count() == 0)?'display:none':''?>" class=cart-icon-container>
                                    <span class=header-cart-icon>
                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        <span class="items" id="as_cart_counter"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                                    </span>
                                </a>
						    <?php // } ?>
							<div class="login"><?php wp_loginout(); ?></div>
						</div>
                        <?php if ( ($this_user != 'club-member') AND ($this_user != 'club-manager') ) { ?>
                            <div class="header-message">
								<p>
                                <?php if ( get_field('header_message', 'option') == '' ) {
									if ( get_field('email_signup_header_text', 'option') != '') {
									?>	
										<span><?php the_field('email_signup_header_text', 'option'); ?></span>
									<?php }
								} else { ?>
										<a style="color:white" href="https://lummiislandwild.com/our-seafood/fill-your-freezer-sale/">
											<?php the_field('header_message', 'option'); ?>
										</a>
								<?php } ?>
								</p>
                            </div>
                        <?php } ?>
					</div>
				</div>
				<div class=inner>
					<div class="header-logo-section header-left">
						<div class="header-left-message">
							<div class="header-message-text">		
								"<?php the_field('header_left','option'); ?>"
							</div>
						</div>
						<div class="logo">
							<a href="<?php bloginfo( 'url' ); ?>" id="site-title">
								<img src="<?php bloginfo('template_directory'); ?>/images/logo.svg" alt="Lummi Island Wild Logo" />
							</a>
						</div>
						<div class="header-review">
							<div class="header-review-text">
								<?php the_field('header_review','option'); ?><br /><img src="<?php bloginfo('template_directory'); ?>/images/five-stars.png" />
							</div>
						</div>
					</div>
					<div class="header-right">
						<nav class="menu-main">
                            <?php if (!is_page('4524')) { ?>
                                <?php  wp_nav_menu( array(
                                    'theme_location' => 'primary',
                                    'container' => false,
                                    'menu_id' => 'nav' )); ?>
                            <?php } else { ?>
                                <?php  wp_nav_menu( array(
                                    'theme_location' => 'primary',
                                    'container' => false,
                                    'menu_id' => 'nav' )); ?>
                            <?php } ?>
							<div style="clear:left"></div>
						</nav>

                        <?php if ( 2==3 ) { ?>
						<div class="mobile-menu-container">
							<div class="hamburger-menu">
								<i class="fa fa-bars" aria-hidden="true"></i>
							</div>

							<nav class="mobile-menu">
								<div class="close-btn"><i class="fa fa-times" aria-hidden="true"></i></div>
								<?php  wp_nav_menu( array(
									'theme_location' => 'mobile',
									'container' => false,
									'menu_id' => 'nav' )); ?>
								<div style="clear:left"></div>
							</nav>

						</div><!-- Mobile Menu End -->
                        <?php } ?>
					</div>
					<div style="clear:both"></div>
				</div>
			</header>
		<div class=total-wrap>
			<div class="wrapper">
		<!-- <h2>This is test server</h2> -->
