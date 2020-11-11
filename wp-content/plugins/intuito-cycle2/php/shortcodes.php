<?php

add_shortcode('cycle2-basic','intuito_cycle2_basic');
function intuito_cycle2_basic() {

	$slideshow='
		<div class="cycle-basic-container">
			<div class="cycle-slideshow"
		    data-cycle-pause-on-hover="true"
		    data-cycle-speed="400"
		    >
			    <img src="http://malsup.github.io/images/p1.jpg">
			    <img src="http://malsup.github.io/images/p2.jpg">
			    <img src="http://malsup.github.io/images/p3.jpg">
			    <img src="http://malsup.github.io/images/p4.jpg">
			</div>
		</div>
	';

	return $slideshow;

}

add_shortcode('cycle2-carousel','intuito_cycle2_carousel');
function intuito_cycle2_carousel() {
/*
	wp_enqueue_script(
		'intuito-cycle2-carousel-function', // handle
		plugins_url() . '/intuito-cycle2/scripts/carousel.js', // relative path
		array( 'cycle2' ) //dependencies. jquery is loaded automatically
	);

		wp_enqueue_script(
		'intuito-cycle2-carousel', // handle
		plugins_url() . '/intuito-cycle2/scripts/jquery.cycle2.carousel.js', // relative path
		array( 'cycle2' ) //dependencies. jquery is loaded automatically
	);

	wp_enqueue_script(
		'intuito-cycle2-tile', // handle
		plugins_url() . '/intuito-cycle2/scripts/jquery.cycle2.tile.js', // relative path
		array( 'cycle2' ) //dependencies. jquery is loaded automatically
	);
*/
	$slideshow = '
		<script type="text/javascript" src="http://localhost:8080/chipoletti/wp-content/plugins/intuito-cycle2/scripts/jquery.cycle2.carousel.js?ver=4.8.2"></script>
		<script type="text/javascript" src="http://localhost:8080/chipoletti/wp-content/plugins/intuito-cycle2/scripts/jquery.cycle2.tile.js?ver=4.8.2"></script>
		<script type="text/javascript" src="http://localhost:8080/chipoletti/wp-content/plugins/intuito-cycle2/scripts/carousel.js?ver=4.8.2"></script>
	<style>
		* { -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; }
		#cycle-1 { height:440px; }
		#cycle-1 div { width:100%; }
		#cycle-2 .cycle-slide { border:3px solid #fff; }
		#cycle-2 .cycle-slide-active { border:3px solid #004; }

		#slideshow-1,#slideshow-2 { width: 50%; max-width: 600px; margin: auto }
		#slideshow-2 { margin-top: 10px }
		.cycle-slideshow img { width: 100%; height: auto; display: block; }
	</style>
	<div id="slideshow-1">
	    <p>
	        <a href="#" class="cycle-prev">&laquo; prev</a> | <a href="#" class="cycle-next">next &raquo;</a>
	        <span class="custom-caption"></span>
	    </p>
	    <div id="cycle-1" class="cycle-slideshow"
	        data-cycle-slides="> div"
	        data-cycle-timeout="0"
	        data-cycle-prev="#slideshow-1 .cycle-prev"
	        data-cycle-next="#slideshow-1 .cycle-next"
	        data-cycle-caption="#slideshow-1 .custom-caption"
	        data-cycle-caption-template="Slide {{slideNum}} of {{slideCount}}"
	        data-cycle-fx="tileBlind"
	        >
	        <div><img src="http://malsup.github.io/images/beach1.jpg" width=500 height=500></div>
	        <div><img src="http://malsup.github.io/images/beach2.jpg" width=500 height=500></div>
	        <div><img src="http://malsup.github.io/images/beach3.jpg" width=500 height=500></div>
	        <div><img src="http://malsup.github.io/images/beach4.jpg" width=500 height=500></div>
	        <div><img src="http://malsup.github.io/images/beach5.jpg" width=500 height=500></div>
	        <div><img src="http://malsup.github.io/images/beach6.jpg" width=500 height=500></div>
	        <div><img src="http://malsup.github.io/images/beach7.jpg" width=500 height=500></div>
	        <div><img src="http://malsup.github.io/images/beach8.jpg" width=500 height=500></div>
	        <div><img src="http://malsup.github.io/images/beach9.jpg" width=500 height=500></div>
	    </div>
	</div>

	<div id="slideshow-2">
	    <div id="cycle-2" class="cycle-slideshow"
	        data-cycle-slides="> div"
	        data-cycle-timeout="0"
	        data-cycle-prev="#slideshow-2 .cycle-prev"
	        data-cycle-next="#slideshow-2 .cycle-next"
	        data-cycle-caption="#slideshow-2 .custom-caption"
	        data-cycle-caption-template="Slide {{slideNum}} of {{slideCount}}"
	        data-cycle-fx="carousel"
	        data-cycle-carousel-visible="5"
	        data-cycle-carousel-fluid=true
	        data-allow-wrap="false"
	        >
	        <div><img src="http://malsup.github.io/images/beach1.jpg" width=100 height=100></div>
	        <div><img src="http://malsup.github.io/images/beach2.jpg" width=100 height=100></div>
	        <div><img src="http://malsup.github.io/images/beach3.jpg" width=100 height=100></div>
	        <div><img src="http://malsup.github.io/images/beach4.jpg" width=100 height=100></div>
	        <div><img src="http://malsup.github.io/images/beach5.jpg" width=100 height=100></div>
	        <div><img src="http://malsup.github.io/images/beach6.jpg" width=100 height=100></div>
	        <div><img src="http://malsup.github.io/images/beach7.jpg" width=100 height=100></div>
	        <div><img src="http://malsup.github.io/images/beach8.jpg" width=100 height=100></div>
	        <div><img src="http://malsup.github.io/images/beach9.jpg" width=100 height=100></div>
	    </div>

	    <p>
	        <a href="#" class="cycle-prev">&laquo; prev</a> | <a href="#" class="cycle-next">next &raquo;</a>
	        <span class="custom-caption"></span>
	    </p>
	</div>
	';

	return $slideshow;

}

?>