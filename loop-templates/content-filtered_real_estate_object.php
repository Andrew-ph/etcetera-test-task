<div class="col-12 col-sm-6 col-lg-4">
	<?php the_title( '<h3 class="entry-title">', '</h3>' );?>
    <h4>Apartment Features</h4>
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
</div>
<!-- /.col -->
