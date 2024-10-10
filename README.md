
Step-by-Step Guide: Fetching Product Price from Site A and Displaying it on Site B

Let’s go step by step on how to fetch the product price from Site A, store the necessary code on Site B, and properly display the price using Elementor.

Prerequisites:

1. Access to WordPress for both Site A and Site B.

2. API keys for accessing product data from Site A.

3. Basic knowledge of managing files and using WordPress themes.

Step 1: Fetch Product Price from Site A

First, you need to enable the WooCommerce API on Site A and get the necessary API keys.

1.1. Enabling API on Site A:

1. Go to the WordPress dashboard of Site A.

2. Navigate to WooCommerce > Settings from the left-hand menu.

3. Click on the Advanced tab and then select REST API.

4. Click Add Key.

5. For the Key Name, enter something like "API for Site B".

6. Set the permissions to Read (Read-only access).

7. After creating the key, you will be given two values: Consumer Key and Consumer Secret. Copy and save them as you will need them later.

Step 2: Set Up Code on Site B to Fetch the Price

Now, we will move to Site B and write code that connects to the API of Site A and retrieves the product price. This code can be placed in the functions.php file or as a custom plugin.

2.1. Add Code to functions.php:

1. Go to the WordPress dashboard of Site B.

2. Navigate to Appearance > Theme Editor.

3. Open the functions.php file of your theme and add the following code at the end:


function fetch_product_price_from_site_a( $product_id ) 
{

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


This function:

Sends an HTTP request to Site A to retrieve the product price using the WooCommerce API.
Uses the API keys from Site A for authentication.
Processes the API response and extracts the price.

Step 3: Store and Display the Price on Site B

Next, you need to store the price and display it on Site B.

3.1. Create a Function to Store and Display the Price:

Add this code to the functions.php file as well:

function display_product_price_in_site_b( $product_id ) {
    // Fetch the price from Site A
    $price = fetch_product_price_from_site_a( $product_id );

    // Check if the price is numeric
    if ( is_numeric( $price ) ) {
        return wc_price( $price );  // Format and return the price
    }

    return 'Price not available';  // If price is not found
}

This function:

Uses the fetch_product_price_from_site_a() function to retrieve the product price.

Formats the price using wc_price() to ensure it’s displayed correctly with currency.


Step 4: Using Shortcode to Display the Price in Elementor

To make it easy to use the price in Elementor, we will create a shortcode that allows you to display the product price anywhere on Site B.

4.1. Adding a Shortcode:

Add this code to the functions.php file:

function custom_product_price_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'product_id' => '',  // The product ID from Site A
    ), $atts, 'custom_product_price' );

    return display_product_price_in_site_b( $atts['product_id'] );
}
add_shortcode( 'product_price', 'custom_product_price_shortcode' );

You can now use the shortcode to display the product price as follows:

[product_price product_id="123"]

Replace 123 with the actual product ID from Site A.

Step 5: Using Elementor to Display the Price

Now that you have the shortcode, you can use it within Elementor.

5.1. Adding Shortcode in Elementor:

1. Go to the page where you want to display the product price.

2. In the Elementor editor, drag and drop the Shortcode widget onto your page.

3. In the shortcode input field, type:

[product_price product_id="123"]

4. Elementor will automatically fetch and display the product price from Site A.

Step 6: Automatically Update the Price with Cron Jobs

To ensure the prices are updated automatically and regularly, you can set up a cron job.

6.1. Adding a Cron Job:

Add the following code to the functions.php file:

// Schedule a cron job to run every hour
if ( ! wp_next_scheduled( 'update_prices_cron_event' ) ) {
    wp_schedule_event( time(), 'hourly', 'update_prices_cron_event' );
}

// Hook to update product prices
add_action( 'update_prices_cron_event', function() {
    $product_id = 123; // The product ID from Site A
    display_product_price_in_site_b( $product_id );  // Update the product price
});

This cron job will automatically fetch the product price from Site A every hour and update it on Site B.

Conclusion

By following these steps:

1. You activate the API on Site A to fetch product information using API keys.

2. Add the necessary code to Site B’s functions.php to fetch, store, and display the product price from Site A.

3. Use Elementor and a shortcode to easily display the price anywhere on Site B.

4. Automatically update the price using a cron job.

If you encounter any issues or need further assistance, feel free to ask!
