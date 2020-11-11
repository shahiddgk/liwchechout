<h1>jQuery Cycle</h1>
<h2>Instructions</h2>
<p>Use this jQuery library to make all sorts of things cycle</p>
<h3>The jQuery function</h3>
Simple</strong>
<p>An example of a testimonial slider. Many more examples and options available at the <a href="http://jquery.malsup.com/cycle/" target="_blank">jQuery Cycle page</a>. Place iterations in a div or ul and apply cycle to it with an id (in this case "textimonial-slider"</p> 

<strong>jQuery Function</strong>

<xmp>
	$('#testimonial-slider')
                .after('<div id="testimonial-nav">')
                .cycle({ 
                    fx:    'scrollRight', 
                    speed: 'fast',
                    timeout: 0,
                    pager: '#testimonial-nav'
                });
</xmp>

<strong>HTML</strong>

<xmp>
	<div id="testimonial-slider">
		<div class="testimonial-content">
			Say something here
		</div>
		<div class="testimonial-content">
			More things to say
		</div>
		<div class="testimonial-content">
			And Oh so much more
		</div>
	</div>
</xmp>