<?php namespace Tlr\Auth;

use Input;
use DB;
use Tlr\Auth\User;

class UserRepository extends \Repository {

	/**
	 * Dem rulz
	 * @var array
	 */
	protected $rules = [];

	public $messages;

	public function __construct( User $user )
	{
		$this->input = Input::get();
		$this->model = $user;
	}

	public function fill()
	{
		$this->translation->fill( $this->data() );
	}

}
