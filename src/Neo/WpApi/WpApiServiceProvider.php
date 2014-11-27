<?php namespace Neo\WpApi;

use Config;
use Neo\WpApi\WpApi;
use Illuminate\Support\ServiceProvider;

class WpApiServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('neo/wp-api');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Bind the default client service
		$this->app->bind('Neo\WpApi\Service\ServiceInterface', 'Neo\WpApi\Service\GuzzleService');

		// Create a default ioc binding
		$this->app->bind('wp-api', function($app)
		{
			$wp = new WpApi($app['Neo\WpApi\Service\ServiceInterface']);

			$wp->setConfig(Config::get('wp-api::config', []));

			return $wp;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('wp-api');
	}

}
