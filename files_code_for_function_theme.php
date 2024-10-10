<?php

function fetch_product_price_from_site_a( $product_id ) {
    // API URL of Site A
    $api_url = 'https://example-site-a.com/wp-json/wc/v3/products/' . $product_id;

    // API keys from Site A
    $consumer_key = 'ck_your_consumer_key';  // Replace with your actual API key
    $consumer_secret = 'cs_your_consumer_secret';  // Replace with your actual Secret key

    // Send HTTP request to Site A's API
    $response = wp_remote_get( $api_url, array(
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret ),
        ),
    ));

    // Process the API response
    if ( is_wp_error( $response ) ) {
        return 'Error fetching price';  // In case of an error
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );  // Convert JSON response to PHP array

    // Return the product price
    if ( isset( $data['price'] ) ) {
        return $data['price'];
    }

    return 'Price not found';  // If price is not found
}

function display_product_price_in_site_b( $product_id ) {
    // Fetch the price from Site A
    $price = fetch_product_price_from_site_a( $product_id );

    // Check if the price is numeric
    if ( is_numeric( $price ) ) {
        return wc_price( $price );  // Format and return the price
    }

    return 'Price not available';  // If price is not found
}

function custom_product_price_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'product_id' => '',  // The product ID from Site A
    ), $atts, 'custom_product_price' );

    return display_product_price_in_site_b( $atts['product_id'] );
}