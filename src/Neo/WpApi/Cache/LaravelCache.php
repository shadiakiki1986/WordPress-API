<?php namespace Neo\WpApi\Cache;

use Illuminate\Cache\CacheManager;

class LaravelCache implements CacheInterface {

	/**
	 * Cache instance
	 *
	 * @var Illuminate\Cache\CacheManager
	 */
	protected $cache;

	/**
	 * The cache key to use to group the cache elements.
	 *
	 * @var string
	 */
	protected $cachekey;

	/**
	 * Minutes before the cache expires.
	 *
	 * @var integer
	 */
	protected $minutes;

	/**
	 * Class constructor.
	 *
	 * @param Illuminate\Cache\CacheManager $cache
	 * @param string       $cachekey
	 * @param integer       $minutes
	 */
	public function __construct(CacheManager $cache, $cachekey, $minutes = null)
	{
		$this->cache 	= $cache;

		$this->cachekey = $cachekey;

		$this->minutes 	= $minutes;
	}

	/**
	 * Retrieve data from the cache.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return $this->cache->get($key);
	}

	/**
	 * Add data to the cache.
	 *
	 * @param  string   $key
	 * @param  mixed    $value
	 * @param  integer  $minutes
	 * @return mixed
	 */
	public function put($key, $value, $minutes = null)
	{
		if (is_null($minutes) OR ! is_numeric($minutes))
		{
			$minutes = $this->minutes;
		}

		return $this->cache->put($key, $value, $minutes);
	}


	/**
	 * Add paginated data to the cache.
	 *
	 * @param  intger   $currentPage
	 * @param  integer  $perPage
	 * @param  integer  $totalItems
	 * @param  mixed    $items
	 * @param  string   $key
	 * @param  integer  $minutes
	 * @return mixed
	 */
	public function putPaginated($currentPage, $perPage, $totalItems, $items, $key, $minutes = null)
	{
		$cached = new \StdClass;
		$cached->items = $items;
		$cached->perPage = $perPage;
		$cached->totalItems = $totalItems;
		$cached->currentPage = $currentPage;

		if (is_null($minutes) OR ! is_numeric($minutes))
		{
			$minutes = $this->minutes;
		}

		$this->put($key, $cached, $minutes);

		return $cached;
	}

	/**
	 * Checks to see if item exists in cache.
	 *
	 * @param  string   $key
	 * @return boolean  Only returns true if exists and not expired.
	 */
	public function has($key)
	{
		return $this->cache->has($key);
	}

}