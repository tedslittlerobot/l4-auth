<?php namespace Tlr\Auth;

// use Auth;
use Controller;
use Input;
use Redirect;
use URL;
use View;
use I18n\Territory;

class LoginController extends Controller {

	public function __construct( RegistrationRepository $repo )
	{
		$this->repo = $repo;
	}

	/**
	 * Redirect the user to the login page
	 * @return RedirectResponse
	 */
	public function redirectToLogin()
	{
		return Redirect::route('login');
	}

	/**
	 * Show a login form
	 * @return View
	 */
	public function loginForm()
	{
		return View::make('auth.login');
	}

	/**
	 * Attempt to log the user in
	 * @return RedirectResponse
	 */
	public function login()
	{
		if ( Auth::attempt([ 'email' => Input::get('email'), 'password' => Input::get('password'), 'valid_user' => 1 ]) )
		{
			// @TODO: check if the home route exists, else route to root (/)
			return Redirect::intended( URL::route('home') );
		}
		else
		{
			return Redirect::route('login')->withErrors( [ trans('auth.error') ] );
		}
	}

	/**
	 * Log the user out
	 * @return RedirectResponse
	 */
	public function logout()
	{
		Auth::logout();

		return Redirect::route('login');
	}

	/**
	 * Show a user registration form
	 * @return View
	 */
	public function registerForm()
	{
		$countries = Territory::all();

		return View::make('auth.register')
			->with('countries', $countries);
	}


	/**
	 * Register the user
	 * @return RedirectResponse
	 */
	public function register()
	{
		if ( $user = $this->repo->register() )
		{
			// Auth::login( $user );
			return Redirect::route('pending');
		}

		return Redirect::back()
			->withInput()
			->withErrors($this->repo->getErrors());
	}

	// For a logged in user registering a new user
	public function registerNewUserForm()
	{
		return View::make('auth.register-new-user');
	}


	// For a logged in user registering a new user
	public function registerNewUser()
	{
		$registration = $this->repo->registerNewUser();

		if ($registration) {
			return View::make('private.portal')->with('messages', $this->repo->messages);
		} else {
			return Redirect::back()
				->withInput()
				->withErrors($this->repo->messages);
		}
	}

	// For users awaiting approval after applying
	public function pending()
	{
		return View::make('auth.pending');
	}

}
