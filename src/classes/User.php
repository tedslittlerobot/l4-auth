<?php namespace Tlr\Auth;

use IlluminateEloquent;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Hash;

class User extends Eloquent implements UserInterface, RemindableInterface {

	/**
	 * The attributes that are mass assignable.
	 * @var array
	 */
	protected $fillable = ['firstname', 'lastname'];

	/**
	 * The attributes that are hidden
	 * @var array
	 */
	protected $hidden = ['password'];

	/**
	 * Determine if the user has the given permissions
	 * @author Stef Horner      (shorner@wearearchitect.com)
	 * @param  string|array   $permissions
	 * @param  boolean        $strict      if true, a user must have all $permissions
	 * @return boolean
	 */
	public function can( $permissions, $strict = true )
	{
		if ( in_array(Auth::NINJA, $this->permissions) ) return true;

		$userPermissions = $this->permissions;

		foreach ( (array)$permissions as $permission )
		{
			$hasPermission = in_array( $permission, $userPermissions );

			if ( $strict && !$hasPermission )
			{
				return false;
			}
			if ( !$strict && $hasPermission )
			{
				return true;
			}
		}

		return $strict;
	}

	/**
	 * Decode permissions from json
	 * @author Stef Horner      (shorner@wearearchitect.com)
	 * @param  string   $permissions
	 * @return array
	 */
	public function getPermissionsAttribute( $permissions )
	{
		return json_decode($permissions);
	}

	/**
	 * Encode permissions to json
	 * @author Stef Horner      (shorner@wearearchitect.com)
	 * @param  array   $permissions
	 * @return string
	 */
	public function setPermissionsAttribute( $permissions )
	{
		$this->attributes['permissions'] = json_encode((array)$permissions);
	}

	/**
	 * Get the user's name
	 * @author Stef Horner       (shorner@wearearchitect.com)
	 * @return string
	 */
	public function getNameAttribute()
	{
		return "{$this->firstname} {$this->lastname}";
	}

	/**
	 * Capitalise the first letter of names
	 * @author Stef Horner (shorner@wearearchitect.com)
	 * @param  string $name
	 * @return string
	 */
	public function setFirstnameAttribute( $name )
	{
		return $this->attributes['firstname'] = ucwords($name);
	}


	/**
	 * Capitalise the first letter of names
	 * @author Stef Horner (shorner@wearearchitect.com)
	 * @param  string $name
	 * @return string
	 */
	public function setLastnameAttribute( $name )
	{
		return $this->attributes['lastname'] = ucwords($name);
	}

	/**
	 * Hash the password before storing it
	 * @param string $password
	 */
	public function setPasswordAttribute( $password )
	{
		return $this->attributes['password'] = Hash::make( $password );
	}

	///// LOGIN /////

	/**
	 * Get the unique identifier for the user.
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

}
