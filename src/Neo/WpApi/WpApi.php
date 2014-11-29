<?php namespace Neo\WpApi;

use Illuminate\Support\Collection;
use Neo\WpApi\Exception\AuthException;
use Neo\WpApi\Service\ServiceInterface;

class WpApi implements ServiceInterface {

	/**
	 * Client interface instance.
	 *
	 * @var Neo\WpApi\Service\ServiceInterface
	 */
	protected $client;

	/**
	 * The access token
	 *
	 * @var string|false
	 */
	protected $accessToken;

	/**
	 * Configuration.
	 *
	 * @var array
	 */
	protected $config = array();

	/**
	 * Class constructor.
	 *
	 * @param 	Neo\WpApi\Service\ServiceInterface $client
	 * @return 	null
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
		if ( ! $client_id)
		{
			$credentials = array();
		}
		else if (is_array($client_id))
		{
			$credentials = $client_id;
		}
		else
		{
			$credentials = array(
				'client_id'	=> $client_id,
				'client_secret' => $client_secret,
				'username'	=> $username,
				'password'	=> $password,
			);
		}

		return $this->_connect($credentials);
	}

	/**
	 * Connect to WordPress.com using password authentication and
	 * retrieve (then store) the access token.
	 *
	 * @param  array        $credentials
	 * @return string|false
	 */
	protected function _connect($credentials = array())
	{
		// Start the session if not already started...
		if (session_id() == '') session_start();

		if ( ! $accessToken = $this->client->getAccessToken())
		{
			$client_id = array_get($credentials, 'client_id', $this->config('client_id'));

			$secret = array_get($credentials, 'client_secret', $this->config('client_secret'));

			$username = array_get($credentials, 'username', $this->config('username'));

			$password = array_get($credentials, 'password', $this->config('password'));

			try
			{
				// Connect to WordPress.com
				$wp = $this->client->connect($client_id, $secret, $username, $password);

				// Retrieve the access token
				$accessToken = $wp->get('access_token');
			}
			catch (AuthException $e)
			{
				$accessToken = false;
			}
		}

		// Get the wordpress site ID
		$site_id = array_get($credentials, 'site_id', $this->config('site_id'));

		// Set stuff for the client...
		$this->client
			->setSiteId($site_id)
			->setAccessToken($accessToken);

		return $this;
	}

	/**
	 * Make a request to the WordPress APi.
	 *
	 * @param  string $http_method
	 * @param  string $path
	 * @param  array  $options
	 * @param  array  $headers
	 * @return Neo\WpApi\WpApi
	 */
	public function api($http_method, $path, $options = array(), $headers = array())
	{
		$this->lastApiResult = $this->client->api($http_method, $path, $options, $headers);

		return $this;
	}

	/**
	 * Return the result as JSON.
	 *
	 * @return string
	 */
	public function toJson()
	{
		return $this->lastApiResult;
	}

	/**
	 * Return the result as Laravel collection.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function toCollection()
	{
		return $this->client->toCollection($this->lastApiResult);
	}

	/**
	 * Return the result as an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return json_decode($this->toJson(), true);
	}

	/**
	 * Get the site information.
	 *
	 * @param  string $key
	 * @return mixed|Illuminate\Support\Collection
	 */
	public function getSiteInfo($key = false)
	{
		return $this->client->getSiteInfo($key);
	}

	/**
	 * Get the total amount of posts.
	 *
	 * @return int
	 */
	public function getPostCount()
	{
		return $this->getSiteInfo()->get('post_count', 0);
	}

	/**
	 * Get posts from the Wordpress API.
	 *
	 * @param  int $page
	 * @param  int $limit
	 * @return Illuminate\Support\Collection
	 */
	public function postsByPage($page, $limit = 10)
	{
		$posts = $this->client->postsByPage($page, $limit);

		return new Collection(array(
			'total'	=> $this->getPostCount(),
			'items'	=> $posts->all()
		));
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
	 * Set the configuration values.
	 *
	 * @param  array $config
	 * @return Neo\WpApi\WpApi
	 */
	public function setConfig(array $config)
	{
		$this->config = $config;

		return $this;
	}

	/**
	 * Get a configuration item value.
	 *
	 * @param  string  $key
	 * @param  mixed $default
	 * @return mixed
	 */
	protected function config($key, $default = false)
	{
		return array_get($this->config, $key, $default);
	}

}