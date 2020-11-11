<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */
if (!defined('ABSPATH')) {
    exit;
}

if ($related_products) :
    ?>

    <section class="related products">
        <h2><?php esc_html_e('Related products', 'woocommerce'); ?></h2>
        <?php woocommerce_product_loop_start(); ?>
        <?php
        global $post;
        $related = get_post_meta($post->ID, '_wcrp_related_ids', true);
        if (isset($related) && !empty($related)) { // remove category based filtering
            $related_products = array();
            $copy = array();
            $related = array_diff($related, array($post->ID));
            $related_products = $related;
            while (count($related_products)) {
                // takes a rand array elements by its key
                $element = array_rand($related_products);
                // assign the array and its value to an another array
                $copy[$element] = $related_products[$element];
                //delete the element from source array
                unset($related_products[$element]);
            }
            foreach ($copy as $related_product) :
                    $post_object = get_post($related_product);
                    setup_postdata($GLOBALS['post'] = & $post_object);
                    wc_get_template_part('content', 'product');
            endforeach;
        } else {
            foreach ($related_products as $related_product) :
                $post_object = get_post($related_product->get_id());
                setup_postdata($GLOBALS['post'] = & $post_object);
                wc_get_template_part('content', 'product');
                ?>
        <?php
        endforeach;
    }
    ?>
    <?php woocommerce_product_loop_end(); ?>
    </section>

<?php
endif;
wp_reset_postdata();
