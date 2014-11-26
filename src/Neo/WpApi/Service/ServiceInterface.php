<?php namespace Neo\WpApi\Service;

interface ServiceInterface {

	/**
	 * Connect to the Wordpress API using a username and password combination.
	 *
	 * @param  string 		$client_id
	 * @param  string		$client_secret
	 * @param  string		$username
	 * @param  string 		$password
	 * @return object
	 * @throws Neo\WpApi\Exception\AuthException
	 */
	function connect($client_id, $client_secret, $username, $password);

	/**
	 * Get the site information.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	function getSiteInfo($key);

	/**
	 * Get posts from the Wordpress API.
	 *
	 * @return Illuminate\Support\Collection
	 */
	function postsByPage($page, $limit);

	/**
	 * Get post by ID.
	 *
	 * @param  int $id
	 * @return string
	 */
	function postById($id);

	/**
	 * Make API calls to the Wordpress API using the "request" method.
	 *
	 * @param  string $http_method
	 * @param  string $path
	 * @param  array $options
	 * @param  array $headers
	 * @return string
	 */
	function api($http_method, $path, $options, $headers);

}