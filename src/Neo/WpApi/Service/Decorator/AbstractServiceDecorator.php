<?php namespace Neo\WpApi\Service\Decorator;

use Neo\WpApi\Service\ServiceInterface;

abstract class AbstractServiceDecorator implements ServiceInterface {

	/**
	 * Class constructor.
	 *
	 * @param  Neo\WpApi\Service\AbstractServiceDecorator  $client
	 * @return null
	 */
	public function __construct(ServiceInterface $client)
	{
		$this->client = $client;
	}

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
	public function connect($client_id = false, $client_secret = false, $username = false, $password = false)
	{
		$this->client->connect($client_id, $client_secret, $username, $password);

		return $this;
	}

	/**
	 * Get the site information.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	public function getSiteInfo($key = false)
	{
		return $this->client->getSiteInfo($key);
	}

	/**
	 * Get posts from the Wordpress API.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function postsByPage($page, $limit = 10)
	{
		return $this->client->postsByPage($page, $limit);
	}

	/**
	 * Get post by ID.
	 *
	 * @param  int $id
	 * @return string
	 */
	public function postById($id)
	{
		return $this->client->postById($id);
	}

	/**
	 * Make API calls to the Wordpress API using the "request" method.
	 *
	 * @param  string $http_method
	 * @param  string $path
	 * @param  array $options
	 * @param  array $headers
	 * @return string
	 */
	public function api($http_method, $path, $options, $headers)
	{
		return $this->client->api($http_method, $path, $options, $headers);
	}
}