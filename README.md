# WordPress API

This package allows Laravel users to interact with the WordPress API. I personally use this as a way to abstract the WordPress backend from the frontend of the website.

### Usage

Usage is simple as you can hope...

	use Neo\WpApi\WpApi;
	use Neo\WpApi\Service\GuzzleService;

	// Het the instance of the WP Api
    $wp = new WpApi(new GuzzleService);

    // Set the configuration
    $config = array(
    	'client_id' 	=> '',
    	'client_secret' => '',
    	'username' 		=> '',
    	'password' 		=> '',
    	'site_id'		=> '')
    );

    // Connect to the API
    $wp = $wp->setConfig($config)->connect();

    // Make your API calls

    $posts = $wp->postsByPage(1, 10);

    $post = $wp->postById(400);

    $post_likes = $wp->api('GET', 'sites/$site_id/posts/$post_id/likes');