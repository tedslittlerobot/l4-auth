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
	 */
	public function routes( $events )
	{
		$events->listen('routes.start', function( $router ) use ( $events )
		{
			$router->group( [ 'before' => 'guest' ], function () use ( $router, $events )
			{
				$events->fire('router.public', array( $router ));
			} );

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
			$router->any('logout', [ 'as' => 'logout', 'uses' => 'Tlr\Auth\LoginController@logout' ]);
		});

		$events->listen('routes.public', function( $router )
		{
			$router->get('log/me/in', [ 'as' => 'login', 'uses' => 'Tlr\Auth\LoginController@loginForm' ]);
			$router->post('i/am/important', [ 'as' => 'login.attempt', 'uses' => 'Tlr\Auth\LoginController@login', 'before' => 'csrf' ]);

			// PASSWORD RESET

			$router->get('reset/password/request', [ 'as' => 'password.request', 'uses' => 'Tlr\Auth\PasswordResetController@request' ]);
			$router->post('reset/password/request', [ 'as' => 'password.request.process', 'uses' => 'Tlr\Auth\PasswordResetController@processRequest' ]);

			$router->get('reset/password/{token}', [ 'as' => 'password.reset', 'uses' => 'Tlr\Auth\PasswordResetController@reset' ]);
			$router->post('reset/password', [ 'as' => 'password.reset.process', 'uses' => 'Tlr\Auth\PasswordResetController@processReset' ]);
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

	/**
	 * Override method for more shallow file structure
	 * @inheritdoc
	 */
	public function guessPackagePath()
	{
		$path = with(new \ReflectionClass($this))->getFileName();

		return realpath(dirname($path).'/../');
	}

}
