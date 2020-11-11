<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />

		<!-- FAVICON MAGIC -->
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
		<link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
		<link rel="manifest" href="/manifest.json">
		<link rel="mask-icon" href="/safari-pinned-tab.svg" color="#002e6d">
		<meta name="theme-color" content="#002e6d">

		<title>
			Lummi Island Wild
			<?php if (!is_front_page()) {echo' | '; wp_title(); } ?>
		</title>

		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css" media="screen" />
		<link href="<?php bloginfo('template_directory'); ?>/css/style-liw.css" rel="stylesheet">
		<link href="<?php bloginfo('template_directory'); ?>/css/lightbox.css" rel="stylesheet">
		<link rel='stylesheet' href="https://fonts.googleapis.com/css?family=Lora:400,400i,700,700i" type='text/css' media="screen" />
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>
		<link rel="icon" href="<?php bloginfo('template_directory'); ?>/favicon.png" type="image/png" />
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style-temp.css" type="text/css" media="screen" />

        <!--[if lt IE 9]>
    		<script>
            document.createElement('header');
            document.createElement('nav');
            document.createElement('section');
            document.createElement('article');
            document.createElement('aside');
            document.createElement('footer');
            document.createElement('hgroup');
            </script>

            <style>
                header, nav, section, article, aside, footer, hgroup {
                    display: block; }
            </style>

	        <link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style-ie.css">

        <![endif]-->
		<?php
			if (is_user_logged_in()) { ?>
				<style>

				</style>

			<?php } else { ?>
				<style>
					#menu-item-1581 {display:inline-block; }
					.page-hero { margin-top:120px; }
				</style>
			<?php }
		?>

		<?php $user = wp_get_current_user(); //print_r($user);
			if ( in_array( 'club-manager', (array) $user->roles ) OR in_array( 'administrator', (array) $user->roles ) ) { ?>
				<style>
					#menu-item-1517 {display:inline-block;}
					.header { top:32px; }
					.page-hero { margin-top:131px; }
					#wprmenu_bar{top:32px!important;}
					@media screen and (max-width: 782px) {
						#wprmenu_bar{top:46px!important;}
					}
				</style>
		<?php } ?>
		
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
		<?php the_field('header_scripts','option'); ?>
		<?php wp_head(); ?>
	</head>
	<body id="page-<?php global $post; the_ID(); ?>" <?php echo 'class='.$this_user?> >
			<header class="header" id="header">
				<div class=inner>
					<div class="header-left">
						<div class="logo">
							<a href="<?php bloginfo( 'url' ); ?>" id="site-title">
								<img src="<?php bloginfo('template_directory'); ?>/images/logo.svg" alt="Lummi Island Wild Logo" />
							</a>
						</div>
					</div>
					<div class="header-right">
						<nav class="menu-main">
							<?php  wp_nav_menu( array(
								'theme_location' => 'primary',
								'container' => false,
								'menu_id' => 'nav' )); ?>
							<div style="clear:left"></div>
						</nav>
						<?php //if (is_cart_empty()) { echo "empty!"; }
							if ( WC()->cart->get_cart_contents_count() != 0 ) { ?>
								<a href="<?php bloginfo('url'); ?>/cart" class=cart-icon-container>
									<span class=header-cart-icon>
										<i class="fa fa-shopping-cart" aria-hidden="true"></i>
										<span class="items"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
									</span>
								</a>
							<?php }
						?>
						<?php if ( ($this_user == 'club-member') OR ($this_user == 'club-manager') ) { echo "
					<div class=club-name>";
					bloginfo('name'); echo" club &nbsp;&nbsp;|&nbsp;&nbsp; ";
					wp_loginout();
					echo "</div>"; } ?>
					</div>
					<div style="clear:both"></div>
				</div>
			</header>
		<div class=total-wrap>
			<div class="wrapper">
