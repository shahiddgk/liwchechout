<?php
get_header();
$term = get_term_by( 'slug', $_GET['club'], 'clubs');
//print_r($term);
?>
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<h1><?php echo $term->name; //echo ucwords(str_replace('-',' ',$_GET['club'])); ?> Registration</h1>
		<?php
            echo do_shortcode('[lummi_register_form clubs_select_menu = false]');
		?>
    </main><!-- .site-main -->
</div><!-- .content-area -->
<?php get_footer(); ?>