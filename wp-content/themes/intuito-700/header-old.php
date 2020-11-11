<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>
			Lummi Island Wild
			<?php if (!is_front_page()) {echo' | '; wp_title(); } ?>
		</title>
		<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css" type="text/css" media="screen" />
		<link href="<?php bloginfo('template_directory'); ?>/css/lightbox.css" rel="stylesheet">
		<link rel='stylesheet' href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,700' type='text/css' media="screen" />
		<link href='https://fonts.googleapis.com/css?family=Montserrat:300,400,700' rel='stylesheet' type='text/css'>
		<link rel="icon" href="<?php bloginfo('template_directory'); ?>/favicon.png" type="image/png" />
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" type="text/css">
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
			if (is_user_logged_in()) {
				echo '<style>#menu-item-1517 {display:inline-block;}</style>';
			} else {
				echo '<style>#menu-item-1581 {display:inline-block;}</style>';
			}
		?>
		
		<?php $user = wp_get_current_user(); //print_r($user);
			if ( in_array( 'club-manager', (array) $user->roles ) OR in_array( 'administrator', (array) $user->roles ) ) { ?>
				<style>
					#wprmenu_bar{top:32px!important;}
					@media screen and (max-width: 782px) { 
						#wprmenu_bar{top:46px!important;}
					}
				</style>
			<?php } ?>
		
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
		<?php wp_head(); ?>
	</head>
	<body id="page-<?php global $post; the_ID(); ?>">
		<?php if (is_front_page()) { ?>
				<div class=home-slideshow>
				<div class="cycle-slideshow"
					data-cycle-fx="scrollHorz"
					data-cycle-timeout="0"
					data-cycle-prev="#prev"
					data-cycle-next="#next"
					data-cycle-slides="> div"
					>
					<?php
					if( have_rows('home_slider') ):
						while ( have_rows('home_slider') ) : the_row(); ?>
							<div style="width:100%;">
								<div class=home-slide style="background-image:url('<?php the_sub_field('image'); ?>')">
										<p class="desktop-text">
											<?php if (get_sub_field('text') != '') { ?>
												<?php the_sub_field('text'); ?>				
											<?php } ?>
										</p>
										<p class="responsive-text">
											<?php if (get_sub_field('responsive_text') != '') { ?>
												<?php the_sub_field('responsive_text'); ?>				
											<?php } ?>
										</p>
								</div>
							</div>	  
						<?php endwhile;
					endif;
					?>

				</div>

				<div class="center">
					<a href=# id="prev"><i class="fa fa-angle-left" aria-hidden="true"></i></a> 
					<a href=# id="next"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
				</div>
			</div>
			<style>
				/*.header { height:669px; } */	
			</style>
		<?php } if (is_singular() and !is_page(259) ) { ?> 
			<div class=page-hero style="background-image:url('<?php if (get_field('hero_image') == '') { the_field('default_page_hero','option'); } else { the_field('hero_image'); } ?>');">
			</div>
		<?php } ?>
		<?php if (is_shop()) { ?> 
			<div class=page-hero style="background-image:url('<?php if (get_field('hero_image','502') == '') { the_field('default_page_hero','option'); } else { the_field('hero_image','502'); } ?>');/*height:700px*/">
				<p class="our-seafood-header-text">
					<?php the_field('hero_text','502'); ?>
				</p>
			</div>
			<style>.page-title {margin-top:150px; }</style>
		<?php } ?>
			<header class="header">
				<div class="header-left">
					<div class="header-title">
						<a href="<?php bloginfo( 'url' ); ?>" id="site-title">
							<!--Lummi Island Wild-->
							<?php bloginfo( 'name' ); ?>
						</a>
                    </div>
					<p style="color: #ffffff; font-size: 13px; margin: 0 21px; width: 600px;"><?php bloginfo( 'description' ); ?></p>
				</div>
				<div class="header-right">
					<nav class="menu-main">
						<?php  wp_nav_menu( array(
							'theme_location' => 'primary',
							'container' => false,
							'menu_id' => 'nav' )); ?>
						<div style="clear:left"></div>
					</nav>
				</div>
				<div style="clear:both"></div>
				<?php if (is_singular() and !is_front_page()) { ?> 
					<div class=page-title>
						<h1>
							<?php echo get_the_title(); ?>
						</h1>
						<?php if (is_page('279')) { echo '<a href="'.get_bloginfo('url').'/lwu-account" class="slideshow-button">Login</a>'; } ?>
					</div>
				<?php } ?>
			</header><!--Close Header-->
		<div class="wrapper">
			