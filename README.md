# WordPress API

This package allows Laravel users to interact with the WordPress API. I personally use this as a way to abstract the WordPress backend from the frontend of the website.

### Usage

Usage is simple as you can hope...

	<?php

	use Neo\WpApi\WpApi;
	use Neo\WpApi\Service\GuzzleService;

	// Get the instance of the WP Api
    $wp = new WpApi(new GuzzleService);

    // Set the configuration
    $config = array(
    	'client_id' 	=> '',
    	'client_secret' => '',
    	'username' 		=> '',
    	'password' 		=> '',
    	'site_id'		=> '',
    );

    // Connect to the API
    $wp = $wp->setConfig($config)->connect();

    // Make your API calls

    $posts = $wp->postsByPage(1, 10);

    $post = $wp->postById(400);

    $post_likes = $wp->api('GET', 'sites/$site_id/posts/$post_id/likes');

### Laravel Users

Laravel users can just include the service provider `Neo\WpApi\WpApiServiceProvider` in their app configuration file.

You can now run `artisan config::publish` to publish the configuration file and customise.

	<?php

	$wp = App::make('wp-api')->connect();

	// Make your api calls...

	$posts = $wp->postsByPage(1);

### Advice

This WordPress API uses the grant "password" type which is meant for **testing purposes**, though I would not mind using it on a real WordPress site since it allows me completely abstract my wordpress site as a back end.

Note that its recommended you use the 2-step authentication and create an Application password in your Wordpress.com dashboard. Using your actual Wordpress password is not recommended.

Enjoy.