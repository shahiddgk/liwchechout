<div class=home-programs>
    <h2 class=home-programs-title>Get Involved</h2>
            <?php
            $my_query = new WP_Query(array(
                'post_type' => 'programs',
                'posts_per_page' => 2
                    ));
            while ($my_query->have_posts()) : $my_query->the_post();
                if (has_post_thumbnail()) {
                    $thumb_url = wp_get_attachment_image_src(get_post_thumbnail_id($my_query->post->ID), 'single-post-thumbnail');
                }
                ?>
        <div class=single-program style="background:url('<?php echo $thumb_url[0]; ?>') right bottom no-repeat; ">
            <div class="single-inner">
                <h3>
                    <a href="<?php the_field('program_link') ?>">
    <?php the_title(); ?>
                    </a>
                </h3>
    <?php the_content(); ?>
                <a class="program-link" href="<?php the_field('program_link') ?>">Learn More</a>
            </div>
        </div>
<?php endwhile; wp_reset_postdata();?>
    <div class=clear></div>
</div>