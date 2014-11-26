<?php namespace Neo\WpApi\Service;

use Neo\WpApi\Exception\AuthException;

class GuzzleService extends ServiceAbstract implements ServiceInterface {

	/**
	 * Class constructor.
	 *
	 * @return null
	 */
	public function __construct()
	{
		$this->guzzle = new \Guzzle\Http\Client;
	}

	/**
	 * Connect to the Wordpress API using a username and password combination.
	 *
	 * @param  string $client_id
	 * @param  string $client_secret
	 * @param  string $username
	 * @param  string $password
	 * @return Illuminate\Support\Collection
	 * @throws Neo\WpApi\Exception\AuthException
	 */
	public function connect($client_id, $client_secret, $username, $password)
	{
		$options = array(
			'_data'	=> array(
				'client_id'		=> $client_id,
				'client_secret'	=> $client_secret,
				'username'		=> $username,
				'password'		=> $password,
				'grant_type'	=> 'password',
			)
		);

		$response = $this->api('POST', $this->apiOauthTokenUrl, $options);

		$collection = $this->toCollection($response);

		if ( ! $collection->get('access_token'))
		{
			throw new AuthException('Unable to retrieve access token.');
		}

		return $collection;
	}

	/**
	 * Get the site information.
	 *
	 * @param  string $key
	 * @return mixed|Illuminate\Support\Collection
	 */
	public function getSiteInfo($key = false)
	{
		$response = $this->api('GET', 'sites/'.$this->getSiteId());

		$siteInfo = $this->toCollection($response);

		return $key ? $siteInfo->get($key) : $siteInfo;
	}

	/**
	 * Get posts from the Wordpress API.
	 *
	 * @return Illuminate\Support\Collection
	 */
	public function postsByPage($page = 1, $limit = 10)
	{
		$options = array(
			'_data' => array(
				'page' => $page,
				'number' => $limit
			)
		);

		$response = $this->api('GET', 'sites/'.$this->getSiteId().'/posts', $options);

		return $this->toCollection($response);
	}

	/**
	 * Get post by ID.
	 *
	 * @param  int $id
	 * @return Illuminate\Support\Collection
	 */
	public function postById($id)
	{
		$response = $this->api('GET', 'sites/'.$this->getSiteId().'/posts/'.$id);

		return $this->toCollection($response);
	}

	/**
	 * Send a Guzzle request to the WordPress API.
	 *
	 * @param  string $type
	 * @param  string $url
	 * @param  array  $data
	 * @param  array  $options
	 * @return string
	 */
	protected function request($type, $url, $data = array(), $options = array())
	{
		switch (strtoupper($type))
		{
			case 'POST':
				$request = $this->guzzle->post($url, [], $data, $options);
				break;

			default:
				$request = $this->guzzle->get($url, $data, $options);
				break;
		}

		return (string) $request->send()->getBody();
	}

}