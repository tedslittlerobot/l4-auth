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

		$this->routes( $this->app['events'] );

		$this->filters( $this->app['router'] );
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
	 * @param  Router $router
	 * @TODO: register some routes!
	 */
	public function routes( $events )
	{
		$events->listen('routes.start', function( $router ) use ( $events )
		{
			$router->get('log/me/in', [ 'as' => 'login', 'uses' => 'Tlr\Auth\LoginController@loginForm', 'before' => 'guest' ]);
			$router->post('i/am/important', [ 'as' => 'login.attempt', 'uses' => 'Tlr\Auth\LoginController@login', 'before' => 'guest' ]);

			$router->group( ['before' => 'auth'], function() use ( $router, $events )
			{
				$events->fire('routes.private', array( $router ));

				$router->group( ['prefix' => 'admin'], function() use ( $router, $events )
				{
					$events->fire('routes.admin', array( $router ));
				} );
			} );
		});

		$events->listen('routes.private', function( $router )
		{
			$router->any('logout', [ 'as' => 'admin', 'uses' => 'Tlr\Auth\LoginController' ]);
		});
	}

	/**
	 * Set up some filters
	 * @param  Router $router
	 * @todo set up some filters
	 */
	public function filters( $router ) { }

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
