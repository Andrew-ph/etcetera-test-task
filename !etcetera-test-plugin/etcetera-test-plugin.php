<?php
/*
Plugin Name: Etcetera-test plugin
Plugin URI: https://etsetera-test.aptiorweb.com/
Description: CPT, custom taxonomy, etc according to the task
Version: 0.1
Author: Andriy Tkachenko
*/
/*  Copyright 2022  Andriy Tkachenko  (email: tkachenko.aat@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*******************************************************************************************************************
Create "Real estate object" CPT
 *******************************************************************************************************************/

function real_estate_post_type() {
	register_post_type( 'real_estate_object',
		array(
			'labels' => array(
				'name' => __( 'Real estate object' ),
				'singular_name' => __( 'Real estate object' )
			),
			'public' => true,
			'show_in_rest' => true,
			'supports' => array('title', 'editor', 'thumbnail'),
			'has_archive' => true,
			'rewrite'   => array( 'slug' => 'real-estate-object' ),
			'taxonomies' => array('district'),
			'menu_icon'         => 'dashicons-pressthis',
			'menu_position'       => 2,
		)
	);
}
add_action( 'init', 'real_estate_post_type' );

/*******************************************************************************************************************
Add "district" taxonomy
 *******************************************************************************************************************/

function create_district_taxonomy() {
	register_taxonomy('district','real_estate_object',array(
		'hierarchical' => false,
		'labels' => array(
			'name' => _x( 'district', 'taxonomy general name' ),
			'singular_name' => _x( 'District', 'taxonomy singular name' ),
			'menu_name' => __( 'District' ),
			'all_items' => __( 'All Districts' ),
			'edit_item' => __( 'Edit District' ),
			'update_item' => __( 'Update District' ),
			'add_new_item' => __( 'Add District' ),
			'new_item_name' => __( 'New District' ),
		),
		'show_ui' => true,
		'show_in_rest' => true,
		'show_admin_column' => true,
	));
}
add_action( 'init', 'create_district_taxonomy', 0 );



/***************************************************************************************************************
Get ACF fields list for CPT (sorted unique names)
 ***************************************************************************************************************/

function get_fields_list_of_cpt( $post_type, $field ){

	// query args

	$args = array(
		'post_type' => $post_type,
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	);

	$loop = new WP_Query( $args );

	// build an array of post IDs

	$postids = array();
	$fields_list = array();

	while ( $loop->have_posts() ) : $loop->the_post();
		array_push($postids, get_the_ID());
	endwhile;

	// get fields values list

	$i = 0;
	foreach ($postids as $postID) {

		// get fields and names if they are in the main list

		if ( get_field( $field, $postID ) ) {
			$fields_list[$i]["field"]= $field;
			$fields_list[$i]["value"]= get_field( $field, $postID );
			$i++;
		}

		// get fields if they are in the sub-fields

		$OutputAllCustomFields = get_post_custom(); // Get all the data
		foreach ( $OutputAllCustomFields as $name => $value ) { // Loop all fields to check if they has needed subfield
			foreach ( $value as $nameAr => $valueAr ) {
				if ( have_rows( $valueAr, $postID ) ) {
					while ( have_rows( $valueAr, $postID ) ) : the_row();
						if ( get_sub_field( $field, $postID ) ) {
							$fields_list[$i]["field"]= $field;
							$fields_list[$i]["value"]= get_sub_field( $field, $postID );
							$i++;
						}
					endwhile;
				}
			}
		}
	}

	// Get only unique names

	$i = 0;
	$temp_values = array();
	$unique_values_array = array();
	foreach($fields_list as $field) {
		if (!in_array($field["value"], $temp_values)) {
			$unique_values_array[$i]["field"] = $field["field"];
			$unique_values_array[$i]["value"] = $field["value"];
		}
		array_push($temp_values, $field["value"]);
		$i++;
	}

	// Sort array

	usort($unique_values_array, function ($item1, $item2) {
		return $item1['value'] <=> $item2['value'];
	});

	return $unique_values_array;
}


/***************************************************************************************************************
CPT AJAX filtering
 ****************************************************************************************************************/

add_action('wp_ajax_myfilter', 'filter_function'); // wp_ajax_{ACTION HERE}
add_action('wp_ajax_nopriv_myfilter', 'filter_function');

function filter_function(){
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type'         => 'real_estate_object',
		'post_status'       => 'publish',
		'posts_per_page'    => -1,
		'orderby'           => 'title',
		'order'             => 'ASC',
		'paged'             => $paged,
	);

	// create $args['meta_query'] array if one of the following fields is filled
	if( isset( $_POST['house_name_filter'] ) || isset( $_POST['number_of_storeys_filter'] ) ||
	    isset( $_POST['environmental_friendliness_filter'] ) || isset( $_POST['total_floor_area_filter'] ) ||
	    isset( $_POST['number_of_rooms_filter'] ) || isset( $_POST['bathroom_filter'] ) ||
	    isset( $_POST['balcony_filter'] ) == 'on' )
		$args['meta_query'] = array( 'relation'=>'AND' ); // AND means that all conditions of meta_query should be true

	// if house_name_filter is set
	if( isset( $_POST['house_name_filter'] ) )
		$args['meta_query'][] = array(
			'key' => 'house_name',
			'value' => $_POST['house_name_filter'],
			'compare' => 'LIKE',
		);

	// if number_of_storeys_filter is set
	if( isset( $_POST['number_of_storeys_filter'] ) )
		$args['meta_query'][] = array(
			'key' => 'number_of_storeys',
			'value' => $_POST['number_of_storeys_filter'],
			'compare' => 'LIKE',
		);

	// if environmental_friendliness_filter is set
	if( isset( $_POST['environmental_friendliness_filter'] ) )
		$args['meta_query'][] = array(
			'key' => 'environmental_friendliness',
			'value' => $_POST['environmental_friendliness_filter'],
			'compare' => 'LIKE',
		);

	// if total_floor_area_filter is set
	if( isset( $_POST['total_floor_area_filter'] ) )
		$args['meta_query'][] = array(
			'key' => 'apartment_total_floor_area',
			'value' => $_POST['total_floor_area_filter'],
			'compare' => 'LIKE',
		);

	// if number_of_rooms_filter is set
	if( isset( $_POST['number_of_rooms_filter'] ) )
		$args['meta_query'][] = array(
			'key' => 'apartment_number_of_rooms',
			'value' => $_POST['number_of_rooms_filter'],
			'compare' => 'LIKE',
		);

	// if balcony_filter is set
	if( isset( $_POST['balcony_filter'] ) )
		$args['meta_query'][] = array(
			'key' => 'apartment_balcony',
			'value' => $_POST['balcony_filter'],
			'compare' => 'LIKE',
		);
	// if bathroom_filter is set
	if( isset( $_POST['bathroom_filter'] ) )
		$args['meta_query'][] = array(
			'key' => 'apartment_bathroom',
			'value' => $_POST['bathroom_filter'],
			'compare' => 'LIKE',
		);

	$loop = new WP_Query( $args );

	if( $loop->have_posts() ) :
		echo '<div class="contentInner row justify-content-md-center">';
		while( $loop->have_posts() ): $loop->the_post();
			get_template_part( 'loop-templates/content-filtered_real_estate_object' );
		endwhile;
	else :
		echo 'No posts found';
	endif;

	wp_reset_postdata();
	die();
}

/***************************************************************************************************************
Get the CPT list shortcode
 ****************************************************************************************************************/
function the_CPT_list() {
	//Setup query to show CPT
	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
	$args = array(
		'post_type'      => 'real_estate_object',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'paged'          => $paged,
	);

	$loop = new WP_Query( $args );
	ob_start();
	echo '<div id="response">';
		echo '<div class="contentInner row justify-content-md-center">';
					while ( $loop->have_posts() ) : $loop->the_post();
						get_template_part( 'loop-templates/content-filtered_real_estate_object' );
					endwhile;
		echo '</div>';
	echo '</div>';
	return ob_get_clean(); // return the buffer contents and delete
}
add_shortcode('CPT_list', 'the_CPT_list');

/***************************************************************************************************************
CPT filter shortcode
 ****************************************************************************************************************/
function the_CPT_filter() {
	ob_start();
	// Filtering form
	echo '<form action="' . site_url() . '/wp-admin/admin-ajax.php" method="POST" id="filter">';
	echo '<h2>Real estate objects filter</h2>';
	/* House name dropdown */
	echo '<h3>House features</h3>';
	echo '<label for="house_name_filter">Choose a house name:</label><br/>';
	$fields = get_fields_list_of_cpt( 'real_estate_object', 'house_name' );
	if ( $fields != 0 ) :
		echo '<select class="jquery-filter" name="house_name_filter"><option value="">Select House name...</option>';
		foreach ( $fields as $field ) :
			echo '<option value="' . $field["value"] . '">' . $field["value"] . '</option>'; // ID of the category as the value of an option
		endforeach;
		echo '</select><br/>';
	endif;

	/* Number of storeys */
	echo '<label for="number_of_storeys_filter">Choose Number of storeys:</label><br/>';
	$fields = get_fields_list_of_cpt('real_estate_object', 'number_of_storeys');
	if( $fields != 0) :
		echo '<select class="jquery-filter" name="number_of_storeys_filter"><option value="">Select Number of storeys...</option>';
		foreach ( $fields as $field ) :
			echo '<option value="' . $field["value"] . '">' . $field["value"] . '</option>'; // ID of the category as the value of an option
		endforeach;
		echo '</select><br/>';
	endif;

	/* Environmental friendliness */
	echo '<label for="environmental_friendliness_filter">Choose Environmental friendliness (1-10):</label><br/>';
	$fields = get_fields_list_of_cpt('real_estate_object', 'environmental_friendliness');
	if( $fields != 0) :
		echo '<select class="jquery-filter" name="environmental_friendliness_filter"><option value="">Select Environmental friendliness (1-10)...</option>';
		foreach ( $fields as $field ) :
			echo '<option value="' . $field["value"] . '">' . $field["value"] . '</option>'; // ID of the category as the value of an option
		endforeach;
		echo '</select><br/>';
	endif;
	echo '<h3>House features</h3>';
	/* total_floor_area_filter */
	echo '<label for="total_floor_area_filter">Total floor area:</label><br/>';
	$fields = get_fields_list_of_cpt('real_estate_object', 'total_floor_area');
	if( $fields != 0) :
		echo '<select class="jquery-filter" name="total_floor_area_filter"><option value="">Select Total floor area...</option>';
		foreach ( $fields as $field ) :
			echo '<option value="' . $field["value"] . '">' . $field["value"] . '</option>'; // ID of the category as the value of an option
		endforeach;
		echo '</select><br/>';
	endif;

	/* Number of rooms */
	echo '<label for="number_of_rooms_filter">Choose Number of rooms:</label><br/>';
	$fields = get_fields_list_of_cpt('real_estate_object', 'number_of_rooms');
	if( $fields != 0) :
		echo '<select class="jquery-filter" name="number_of_rooms_filter"><option value="">Number of rooms...</option>';
		foreach ( $fields as $field ) :
			echo '<option value="' . $field["value"] . '">' . $field["value"] . '</option>'; // ID of the category as the value of an option
		endforeach;
		echo '</select><br/>';
	endif;

	echo '
		<label for="balcony_filter">Balcony</label><br/>
		<input class="jquery-filter" type="radio" name="balcony_filter" value="Present">Yes</input><br/>
		<input class="jquery-filter" type="radio" name="balcony_filter" value="Absent">No</input><br/>
	
		<label for="bathroom_filter">Bathroom</label><br/>
		<input class="jquery-filter" type="radio" name="bathroom_filter" value="Present">Yes</input><br/>
		<input class="jquery-filter" type="radio" name="bathroom_filter" value="Absent">No</input><br/>
	
	
		<input id="reset-button" type="reset" value="Clear filters" class="clear-filter"><br/>
		<input type="hidden" name="action" value="myfilter">
	
	</form>';

	wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/ajax-filter.js', array(), null, true );
	return ob_get_clean();
}
add_shortcode('CPT_filter', 'the_CPT_filter');