<script src="http://malsup.github.io/jquery.cycle2.carousel.js"></script>
<script src="http://malsup.github.io/jquery.cycle2.tile.js"></script>

<div id="slideshow-1">
    <div id="cycle-1" class="cycle-slideshow"
        data-cycle-slides="> div"
        data-cycle-timeout="0"
        data-cycle-prev="#slideshow-1 .cycle-prev"
        data-cycle-next="#slideshow-1 .cycle-next"
        data-cycle-caption="#slideshow-1 .custom-caption"
        data-cycle-caption-template="Slide {{slideNum}} of {{slideCount}}"
        data-cycle-pager=".cycle-pager"
        >
        <?php
if( have_rows('home_slideshow') ):
    $i = 1;
    while ( have_rows('home_slideshow') ) : the_row(); ?>
        <div class=home-slide id="home-slide-<?php echo $i; $i++; ?>" style="background-image:url('<?php the_sub_field('image'); ?>');">
            <h1 class=home-title><?php the_sub_field('title'); ?></h1>
            <a href="<?php the_sub_field('button_link'); ?>" class="gold-button"><?php the_sub_field('button_text'); ?></a>
            <?php if ( get_sub_field('caption') != '') { ?>
            <div class="caption">
                <?php the_sub_field('caption'); ?>
            </div>
            <?php } ?>
        </div>
    <?php endwhile;
endif;
?>
    </div>
    <div class="cycle-pager"></div>
    <a href="#" class="cycle-prev"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
    <a href="#" class="cycle-next"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
</div>


<script>
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
</script>