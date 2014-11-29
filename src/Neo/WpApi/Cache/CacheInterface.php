<?php namespace Neo\WpApi\Cache;

interface CacheInterface {

	/**
	 * Retrieve data from the cache.
	 *
	 * @param  string $key
	 * @return mixed
	 */
	public function get($key);

	/**
	 * Add data to the cache.
	 *
	 * @param  string   $key
	 * @param  mixed    $value
	 * @param  integer  $minutes
	 * @return mixed
	 */
	public function put($key, $value, $minutes = null);


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
	public function putPaginated($currentPage, $perPage, $totalItems, $items, $key, $minutes);

	/**
	 * Checks to see if item exists in cache.
	 *
	 * @param  string   $key
	 * @return boolean  Only returns true if exists and not expired.
	 */
	public function has($key);

}