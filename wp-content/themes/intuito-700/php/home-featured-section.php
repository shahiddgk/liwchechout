<div class=feature>
    <div class=inner>
        <div class=left>
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <article class="post" style="font-size:18pt;">
                        <?php the_content(); ?>
                    <?php endwhile;
                else: ?>
                    <p>Sorry, no posts matched your criteria.</p>
				<?php endif; ?>
                <a href="https://vimeo.com/163337697" class="mpopup_iframe blue-button-patagonia" style="width:auto;display:none">
                    <span class=text>Our Story</span>
                </a>
                <a href="https://lummiislandwild.com/our-seafood/" class="blue-button-patagonia" style="width:auto">
                    <span class=text>Shop Our Seafood</span>
                </a>
        </div>
        <div class=right >
			<img src="https://lummiislandwild.com/wp-content/uploads/2016/07/lummi-island-wild-our-story.png" alt="Sepia photo of a fisherman" />
            <?php
			/*
            $image = get_field('feature_image');
            if (!empty($image)) {
                ?>
                <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
			<?php 
			
			} */
			?>
        </div>
    </div>
</div>