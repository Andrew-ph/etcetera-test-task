<?php
/**
 * Single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">


    <header class="entry-header">

        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        <div class="entry-meta">

            <?php understrap_posted_on(); ?>

        </div><!-- .entry-meta -->

    </header><!-- .entry-header -->

    <?php // echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

    <div class="entry-content">
        <?php
            the_content();
        ?>

        <div class="row">
            <div class="col-12 col-sm-6">

                <h2>House Features</h2>
                <ul class="real-estate-fields">
                    <li><span class="field-name">House name</span>: <span class="field-value"></span><?php the_field('house_name'); ?></li>
                    <li><span class="field-name">Coordinates</span>: <span class="field-value"></span><?php the_field('coordinates'); ?></li>
                    <li><span class="field-name">Number of storeys</span>: <span class="field-value"></span><?php the_field('number_of_storeys'); ?></li>
                    <li><span class="field-name">Environmental friendliness</span>: <span class="field-value"></span><?php the_field('environmental_friendliness'); ?></li>
                    <li><span class="field-name">Photo</span>:</li>
                </ul><!-- /.real-estate-fields -->

                    <?php $image = get_field('photo');
                    if( !empty( $image ) ): ?>
                        <img class="house-image" src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                    <?php endif; ?>
            </div><!-- /.col-12 col-sm-6 -->

            <div class="col-12 col-sm-6">
                <h2>Apartment Features</h2>

                <?php
                    if( have_rows('apartment') ):
                        while ( have_rows('apartment') ) : the_row();
                            $total_floor_area = get_sub_field('total_floor_area');
                            $number_of_rooms = get_sub_field('number_of_rooms');
                            $balcony = get_sub_field('balcony');
                            $bathroom = get_sub_field('bathroom');
                            $flat_photo = get_sub_field('flat_photo');
                        endwhile;
                    else :
                        echo('<p>No information found for this record</p>');
                    endif;
                ?>
                <ul class="real-estate-fields">
                    <li><span class="field-name">Total floor area</span>: <span class="field-value"></span><?php echo($total_floor_area) ?></li>
                    <li><span class="field-name">Number of rooms</span>: <span class="field-value"></span><?php echo($number_of_rooms) ?></li>
                    <li><span class="field-name">Balcony</span>: <span class="field-value"></span><?php echo($balcony) ?></li>
                    <li><span class="field-name">Bathroom</span>: <span class="field-value"></span><?php echo($bathroom) ?></li>
                    <li><span class="field-name">Flat photo</span>:</li>
                </ul><!-- /.real-estate-fields -->
                <?php
                if( !empty( $flat_photo ) ): ?>
                    <img class="house-image" src="<?php echo esc_url($flat_photo['url']); ?>" alt="<?php echo esc_attr($flat_photo['alt']); ?>" />
                <?php endif; ?>
            </div><!-- /.col-12 col-sm-6 -->
        </div><!-- /.row -->
    </div><!-- .entry-content -->

    <footer class="entry-footer">

        <?php understrap_entry_footer(); ?>

    </footer><!-- .entry-footer -->

</article><!-- #post-## -->
