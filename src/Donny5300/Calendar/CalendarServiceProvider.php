<?php namespace Donny5300\Calendar;

use Illuminate\Support\ServiceProvider;
use App;
use Artisan;

class CalendarServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['calendar'] = $this->app->share(function($app) {
			return new Calendar;
		});

		$this->app->booting(function() {
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Calendar', 'Donny5300\Calendar\Calendar');
		});
	}

	public function boot(){
		$this->package('donny5300/calendar');

		$app = $this->app;
//		include __DIR__.'/../../routes.php';
	}
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
