<?php

/**
 * Plugin Name: Smart Slider Rest Api
 * Description: Custom plugin to get Smart Slider data
 * Version: 1.0.0
 */

function smart_slider_endpoint() {
    register_rest_route('smart-slider', '/slides', array(
        'methods' => 'GET',
        'callback' => 'get_smart_slider_slides',
    ));
}

add_action('rest_api_init', 'smart_slider_endpoint');

function get_smart_slider_slides($request) {
    global $wpdb;

    $title = $request->get_param('title');
    $orderby = 'ordering'; // Order by the 'ordering' field.

    $query = $wpdb->prepare(
        "SELECT s.id AS slide_id, s.ordering AS slide_ordering, 
        sl.id AS slider_id, s.thumbnail AS thumbnail_id
        FROM {$wpdb->prefix}nextend2_smartslider3_slides AS s
        LEFT JOIN {$wpdb->prefix}nextend2_smartslider3_sliders AS sl ON s.slider = sl.id
        WHERE sl.title = %s
        ORDER BY s.ordering ASC",
        $title
    );

    $results = $wpdb->get_results($query);
    $slides = array();

    if ($results) {
        foreach ($results as $result) {
            // Get the image URL based on the thumbnail ID.
            $thumbnail_url = get_site_url() . '/wp-content/uploads' . str_replace('$upload$', '', $result->thumbnail_id);

            // Customize this part to format the slide data as needed.
            $slide_data = array(
                'slider_id' => $result->slider_id,
                'id' => $result->slide_id,
                'ordering' => $result->slide_ordering,
                'thumbnail' => $thumbnail_url
            );

            $slides[] = $slide_data;
        }
    } 
    return rest_ensure_response($slides);
}

?>
