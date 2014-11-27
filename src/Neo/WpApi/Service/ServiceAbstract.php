<?php namespace Neo\WpApi\Service;

use Illuminate\Support\Collection;
use Neo\WpApi\Exception\ApiException;
use Neo\WpApi\Exception\UnknownPostException;
use Neo\WpApi\Exception\UnknownBlogException;
use Neo\WpApi\Exception\UnauthorizedException;

abstract class ServiceAbstract {

	/**
	 * WordPress API token URL
	 *
	 * @var string
	 */
	protected $apiOauthTokenUrl = 'https://public-api.wordpress.com/oauth2/token/';

	/**
	 * REST API base url.
	 *
	 * @var string
	 */
	protected $apiBaseUrl = 'https://public-api.wordpress.com/rest/v1';

	/**
	 * URL or Wordpress site ID.
	 *
	 * @var string|int
	 */
	protected $site_id;

	/**
	 * Access token used by the client.
	 *
	 * @var string
	 */
	protected $access_token;

	/**
	 * Make a request to the WordPress APi.
	 *
	 * @param  string $http_method
	 * @param  string $path
	 * @param  array  $options
	 * @param  array  $headers
	 * @return string
	 */
	public function api($http_method, $path, $options = array(), $headers = array())
	{
		// Create options
		$options = array_merge(array(
				'headers'	 => ['authorization' => 'Bearer '.$this->getAccessToken()] + $headers,
				'exceptions' => false
			), $options
		);

		// Get data to be sent with request...
		$data = array_get($options, '_data', []);

		switch(strtoupper($http_method))
		{
			case 'POST':
				$response = $this->request('POST', $this->apiUrl($path), $data, $options);
				break;

			default:
				$response = $this->request('GET', $this->apiUrl($path), $data, $options);
				break;
		}

		$this->verifyResponse($response);

		return $response;
	}

	/**
	 * Rest api URL.
	 *
	 * @param  string $suffix
	 * @return string
	 */
	public function apiUrl($suffix)
	{
		if (strpos($suffix, 'http://') === 0 || strpos($suffix, 'https://') === 0)
		{
			$url = $suffix;
		}
		else
		{
			$url = $this->apiBaseUrl.'/'.$suffix;
		}

		return $url;
	}

	/**
	 * Set the Wordpress site ID.
	 *
	 * @param string|int $site_id
	 * @return Object
	 */
	public function setSiteId($site_id)
	{
		$this->site_id = $site_id;

		return $this;
	}

	/**
	 * Get the site ID.
	 *
	 * @return string
	 */
	public function getSiteId()
	{
		return $this->site_id;
	}

	/**
	 * Sets the access token for the client usage.
	 *
	 * @param  string $access_token
	 * @return Object
	 */
	public function setAccessToken($access_token)
	{
		$_SESSION['wpapi'] = $this->access_token = $access_token;

		return $this;
	}

	/**
	 * Get the access token.
	 *
	 * @return string
	 */
	public function getAccessToken()
	{
		if ( ! $this->access_token && array_get($_SESSION, 'wpapi'))
		{
			$this->access_token = array_get($_SESSION, 'wpapi');
		}

		return $this->access_token;
	}

	/**
	 * Parse the response returned from the WordPress API.
	 *
	 * @param  string|array $response
	 * @return Illuminate\Support\Collection
	 */
	public function toCollection($response)
	{
		if (is_string($response) && ($jsonResponse = json_decode($response, true)))
		{
			$response = $jsonResponse;
		}

		return new Collection($response);
	}

	/**
	 * Verify the API response for errors.
	 *
	 * @param  string $response
	 * @return true
	 * @throws Neo\WpApi\Exception\ApiException
	 */
	protected function verifyResponse($response)
	{
		$message = 'An unknown error occurred.';

		if ($response = json_decode($response))
		{
			if (isset($response->error))
			{
				$message = isset($response->message) ? $response->message : $response->error_description;

				switch ($response->error)
				{
					case 'unauthorized' OR 'unknown_client':
						throw new UnauthorizedException($message);
						break;

					case 'unknown_post':
						throw new UnknownPostException($message);
						break;

					case 'unknown_blog':
						throw new UnknownBlogException($message);
						break;

					default:
						# do nothing
						break;
				}
			}
			else
			{
				return true;
			}
		}

		throw new ApiException($response->message);
	}

	/**
	 * Send a direct request to the WordPress API.
	 *
	 * @param  string $type
	 * @param  string $url
	 * @param  array  $data
	 * @param  array  $options
	 * @return string
	 */
	abstract protected function request($type, $url, $data, $options);

}