<?php
/**
 * Template Name: Ajax test template
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );

if ( is_front_page() ) {
    get_template_part( 'global-templates/hero' );
}
?>

    <div class="wrapper" id="full-width-page-wrapper">

        <div class="<?php echo esc_attr( $container ); ?>" id="content">

            <div class="row">

                <div class="col-md-12 content-area" id="primary">

                    <main class="site-main" id="main" role="main">

                        <?php the_content(); ?>

                        <?php echo do_shortcode( '[CPT_list]' ); ?>
                        <?php echo do_shortcode( '[CPT_filter]'); ?>

                    </main><!-- #main -->

                </div><!-- #primary -->

            </div><!-- .row end -->

        </div><!-- #content -->

    </div><!-- #full-width-page-wrapper -->

<?php

get_footer();
