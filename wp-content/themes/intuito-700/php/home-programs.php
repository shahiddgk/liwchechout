<div class=home-programs>
    <h2 class=home-programs-title><?php the_field('bottom_section_title'); ?></h2>
	<?php if ( get_field('home_programs_intro') != '' ) { ?> 
		<div class="home-programs-intro">
			<?php the_field('home_programs_intro'); ?>
		</div>
	<?php } ?>
        <div class="single-program first-program" style="background:url('https://lummiislandwild.com/wp-content/uploads/2017/01/programs-buyers-clubs-300x288.png') right bottom no-repeat; ">
            <div class="single-inner">
                <h3>
					<a href="<?php the_field('left_link') ?>">
						<?php the_field('left_title'); ?>
					</a>
                </h3>
    			<p>
					<?php the_field('left_description'); ?>
				</p>
                <a class="program-link" href="<?php the_field('left_link') ?>">Learn More</a>
            </div>
        </div>
		<div class="single-program second-program" style="background:url('https://lummiislandwild.com/wp-content/uploads/2017/01/programs-ambassodors-300x288.png') right bottom no-repeat; ">
            <div class="single-inner">
                <h3>
                    <a href="<?php the_field('right_link') ?>">
						<?php the_field('right_title'); ?>
                    </a>
                </h3>
    			<p>
					<?php the_field('right_description'); ?>	
				</p>
                <a class="program-link" href="<?php the_field('right_link'); ?>">Learn More</a>
            </div>
        </div>
    <div class=clear></div>
</div>