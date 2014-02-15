<?php namespace Auth;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Tlr\Auth\User;
use Tlr\Auth\UserRepository;

class UsersController extends Controller {

	/**
	 * The index view
	 * @var string
	 */
	public static $indexView = 'l4-auth::user.index';

	/**
	 * The edit view
	 * @var string
	 */
	public static $editView = 'l4-auth::user.edit';

	/**
	 * The show view
	 * @var string
	 */
	public static $showUserView = 'l4-auth::user.show';

	/**
	 * The edit profile view
	 * @var string
	 */
	public static $editProfileView = 'l4-auth::user.profile';


	public function __construct( UserRepository $repository )
	{
		$this->repo = $repository;
	}

	/**
	 * Get the index of users
	 * @return View
	 */
	public function index()
	{
		$query = User::query();

		$query->orderBy( Input::get('order', 'lastname'), Input::get('direction', 'ASC') );

		if ( $filter = Input::get('filter') )
		{
			$query->where('lastname', 'LIKE', substr($filter, 0, 1) . '%');
		}

		$users = $query->paginate();

		return View::make( self::$indexView )
			->with( 'users', $users );
	}

	/**
	 * Edit a user
	 * @param  User   $user
	 * @return View
	 */
	public function edit( User $user )
	{
		return View::make( self::$editView )
			->with('user', $user);
	}

	/**
	 * Edit a user
	 * @param  User   $user
	 * @return RedirectResponse
	 */
	public function update( User $user )
	{
		if ( ! $user = $this->repo->update( $user ) )
		{
			return Redirect::back()
				->withInput()
				->withErrors( $this->repo->getErrors() );
		}

		return Redirect::route('');
	}

	/**
	 * Edit the currently logged in user
	 * @return View
	 */
	public function editProfile()
	{
		return $this->edit( Auth::user() );
	}

	/**
	 * Update the currently logged in user
	 * @return RedirectResponse
	 */
	public function updateProfile()
	{
		return $this->update( Auth::user() );
	}

}
