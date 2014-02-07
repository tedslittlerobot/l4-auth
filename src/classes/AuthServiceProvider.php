<?php namespace Tlr\Auth;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider {

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
		$this->commands('Tlr\Auth\UserMakeCommand');
	}

	/**
	 * Set up some boot actions
	 */
	public function boot()
	{
		$this->package('tlr/l4-auth');

		$this->userEvents( $this->app['events'] );

		$this->routes();
	}

	/**
	 * Some User events
	 * @param  Illuminate\Events\Dispatcher $event
	 * @todo: move this somewhere more relevant
	 */
	public function userEvents( $event )
	{
		$event->listen('auth.login', function($user, $remember)
		{
			$user->last_login = new Carbon;
			$user->save();
		});
	}

	/**
	 * Register some routes
	 * @TODO: register some routes!
	 */
	public function routes() { }

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
