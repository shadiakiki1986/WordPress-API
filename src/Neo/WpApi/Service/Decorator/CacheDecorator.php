<?php namespace Neo\WpApi\Service\Decorator;

use Neo\WpApi\WpApi;
use Neo\WpApi\Cache\CacheInterface;

class CacheDecorator extends AbstractServiceDecorator {

	/**
	 * Cache instance
	 *
	 * @var Neo\WpApi\Cache\CacheInterface
	 */
	protected $cache;

	/**
	 * Class constructor.
	 *
	 * @param  Neo\WpApi\WpApi 					$client
	 * @param  Neo\WpApi\Cache\CacheInterface   $cache
	 * @return null
	 */
	public function __construct(WpApi $api, CacheInterface $cache)
	{
		parent::__construct($api);

		$this->cache = $cache;
	}

	/**
	 * Get the site information.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	public function getSiteInfo($key = false)
	{
		if ($this->cache->has($key))
		{
			return $this->cache->get($key);
		}

		$response = $this->client->getSiteInfo($key);

		$this->cache->put($key, $response);

		return $response;
	}

	/**
	 * Get posts from the Wordpress API.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function postsByPage($page, $limit)
	{
		$key = "posts_{$page}_{$limit}";

		if ($this->cache->has($key))
		{
			return $this->cache->get($key);
		}

		$response = $this->client->postsByPage($page, $limit);

		$this->cache->put($key, $response);

		return $response;
	}

	/**
	 * Get post by ID.
	 *
	 * @param  int $id
	 * @return string
	 */
	public function postById($id)
	{
		$key = "post_{$id}";

		if ($this->cache->has($key))
		{
			return $this->cache->get($key);
		}

		$response = $this->client->postById($id);

		$this->cache->put($key, $response);

		return $response;
	}

	/**
	 * Cache API calls to the Wordpress API.
	 *
	 * @param  string $http_method
	 * @param  string $path
	 * @param  array $options
	 * @param  array $headers
	 * @return string
	 */
	public function api($http_method, $path, $options = array(), $headers =array())
	{
		$key = '';

		$args = func_get_args();

		// Serialize the arguments of the function...
		foreach($args as $arg) $key .= serialize($arg);

		$key = md5($key);

	    if ($this->cache->has($key))
	    {
			return $this->cache->get($key);
	    }

	    // Get the response and save it...
	    $response = $this->client->api($http_method, $path, $options, $headers);

	    // Save the response
	    $this->cache->put($key, $response);

	    return $response;
	}

}