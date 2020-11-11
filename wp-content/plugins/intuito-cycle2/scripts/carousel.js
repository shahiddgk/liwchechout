jQuery(document).ready(function($){

	var slideshows = $('.cycle-slideshow').on('cycle-next cycle-prev', function(e, opts) {
	    // advance the other slideshow
	    slideshows.not(this).cycle('goto', opts.currSlide);
	});

	$('#cycle-2 .cycle-slide').click(function(){
	    var index = $('#cycle-2').data('cycle.API').getSlideIndex(this);
	    slideshows.cycle('goto', index);
	});

});