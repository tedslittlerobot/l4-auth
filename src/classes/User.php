<?php namespace Tlr\Auth;

use Tlr\Auth\AuthFacade as Auth;
use Illuminate\Database\Eloquent\Model as Eloquent;
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
	 * @param  string   $permissions
	 * @return array
	 */
	public function getPermissionsAttribute( $permissions )
	{
		return json_decode($permissions);
	}

	/**
	 * Encode permissions to json
	 * @param  array   $permissions
	 * @return string
	 */
	public function setPermissionsAttribute( $permissions )
	{
		if (is_array($permissions))
		{
			$this->attributes['permissions'] = json_encode((array)$permissions);
		}
	}

	/**
	 * Add one or more permissions
	 * @param string|array $input
	 * @return  array                         All allowed permissions
	 */
	public function addPermission( $input )
	{
		$permissions = $this->permissions;

		foreach ( (array)$input as $permission )
		{
			if ( ! in_array($permission, $permissions) )
			{
				$permissions[] = $permission;
			}
		}

		return $this->permissions = $permissions;
	}

	/**
	 * Remove one or more permissions
	 * @param string|array $input
	 * @return  array                         All allowed permissions
	 */
	public function denyPermission( $input )
	{
		$permissions = $this->permissions;

		foreach ( (array)$input as $permission )
		{
			if ( $index = array_search($permission, $permissions) )
			{
				unset( $permissions[ $index ] );
			}
		}

		return $this->permissions = array_values($permissions);
	}

	/**
	 * Sync permissions. If the second argument is null or omitted, Auth::$_PERMISSIONS
	 * will be used
	 * @param  string|array $input            The permissions to allow
	 * @param  array        $allPermissions   The list of all permissions
	 * @return  array                         All allowed permissions
	 */
	public function syncPermissions( $input, $allPermissions = null )
	{
		if ( is_null($allPermissions) )
		{
			$allPermissions = Auth::$_PERMISSIONS;
		}

		$permissions = [];

		foreach ( (array)$input as $permission )
		{
			if ( in_array($permissions, $allPermissions) )
			{
				$permissions[] = $permission;
			}
		}

		if ( in_array(Auth::NINJA, $this->permissions) )
		{
			$permissions[] = 'ninja';
		}

		return $this->permissions = $permissions;
	}

	/**
	 * Get the user's name
	 * @return string
	 */
	public function getNameAttribute()
	{
		return "{$this->firstname} {$this->lastname}";
	}

	/**
	 * Capitalise the first letter of names
	 * @param  string $name
	 * @return string
	 */
	public function setFirstnameAttribute( $name )
	{
		return $this->attributes['firstname'] = ucwords($name);
	}


	/**
	 * Capitalise the first letter of names
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

	/**
	 * @inheritdoc
	 */
	public function getDates()
	{
		return array_merge( parent::getDates(), [ 'registered_at', 'last_login' ] );
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
