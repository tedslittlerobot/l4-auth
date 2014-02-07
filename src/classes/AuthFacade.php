<?php namespace Tlr\Auth;

use Illuminate\Support\Facades\Auth as Facade;

class AuthFacade extends Facade {

	/*
	|--------------------------------------------------------------------------
	| Auth Levels
	|--------------------------------------------------------------------------
	|
	| Define some constants for auth levels. To macro constants together use
	| public static properties.
	|
	*/

	/**
	 * The ninja can get by all auth checks. They are to be feared.
	 * Wisdom: 13
	 * Dexterity: 13
	 * Charisma: n/a (has never been seen)
	 */
	const NINJA = 'ninja';

	/**
	 * An array of permissions
	 * @var array
	 */
	public static $_PERMISSIONS = [

		/**
		 * The admin can do is view the admin screen - this is the most basic administration
		 * privilege.
		 * Wisdom: 2
		 * Dexterity: 1
		 * Charisma: 5
		 */
		'admin',

		/**
		 * The user manager is one who holds the life of the users in their hands. They alone
		 * are the gatekeepers both in and out membership.
		 * permission.
		 * Wisdom: 9
		 * Dexterity: 2
		 * Charisma: 3
		 */
		'manage-users',

	];

}
