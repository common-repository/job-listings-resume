<?php
/**
 * Display resume package form
 *
 * This template can be overridden by copying it to yourtheme/job-listings/templates/form/resume-package.php.
 *
 * HOWEVER, on occasion NooTheme will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author      NooTheme
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>
<?php

global $jlt_view_candidate_package;
$jlt_view_candidate_package = true;

$product_args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'suppress_filters' => false,
    'tax_query' => array(
        array(
            'taxonomy' => 'product_type',
            'field' => 'slug',
            'terms' => array('candidate_package')
        )
    ),
    'orderby' => 'menu_order title',
    'order' => 'ASC',
    'suppress_filters' => false
);

$packages = new WP_Query($product_args);
$jlt_view_candidate_package = false;
?>
<?php if ($packages->have_posts()) : ?>
    <?php while ($packages->have_posts()) : $packages->the_post(); ?>
        <ul class="jlt-list-package">
            <?php jlt_get_template_part('package/content', 'item'); ?>
        </ul>
    <?php endwhile; ?>
    <?php wp_reset_postdata();
endif;
?>