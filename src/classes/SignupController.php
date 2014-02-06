<?php namespace Tlr\Auth;

// use Auth;
use Controller;
use Input;
use Redirect;
use URL;
use View;
use I18n\Territory;

class SignupController extends Controller {

	public function __construct( RegistrationRepository $repo )
	{
		$this->repo = $repo;
	}

	/**
	 * Show a user registration form
	 * @author Stef Horner (shorner@wearearchitect.com)
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
	 * @author Stef Horner (shorner@wearearchitect.com)
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

	public function handleRegisterToken($token)
	{
		return 'LOGIN!!!';
	}

}
