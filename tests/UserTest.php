<?php

use Illuminate\Support\Facades\Hash;
use Mockery as m;

class MenuRepositoryTest extends PHPUnit_Framework_TestCase {

	public function setUp() {
		parent::setUp();

		$this->user = new Tlr\Auth\User;
	}

	public function tearDown()
	{
		m::close();
	}

	public function testAuthLevelAccessorAndMutator()
	{
		$this->user->permissions = ['foo', 'bar'];

		$this->assertEquals( [ 'permissions' => json_encode(['foo', 'bar']) ], $this->user->getAttributes() );

		$this->assertEquals( ['foo', 'bar'], $this->user->permissions );
	}

	public function testAddPermission()
	{
		$this->user->permissions = [];

		$this->user->addPermission('one');

		$this->assertEquals(['one'], $this->user->permissions);

		$this->user->addPermission( ['one', 'two', 'three'] );

		$this->assertEquals(['one', 'two', 'three'], $this->user->permissions);
	}

	public function testNameAccessorsAndMutators()
	{
		$this->user->firstname = 'foo bar';
		$this->user->lastname = 'bar baz';

		$expected = [
			'firstname' => 'Foo Bar',
			'lastname' => 'Bar Baz',
		];

		$this->assertEquals( $expected, $this->user->getAttributes() );

		$this->assertEquals( 'Foo Bar Bar Baz', $this->user->name );
	}

	public function testHashPassword()
	{
		Hash::shouldReceive('make')
			->once()
			->with('foo')
			->andReturn('woop');

		$this->user->password = 'foo';

		$this->assertEquals( 'woop', $this->user->password );
	}

	public function testUserCan()
	{
		$this->user->permissions = [ 'one', 'two', 'three', 'four' ];

		$this->assertEquals( true, $this->user->can('two') );
		$this->assertEquals( false, $this->user->can('woop') );

		$this->assertEquals( false, $this->user->can(['two', 'woop']) );

		// test for weak comparison
		$this->assertEquals( true, $this->user->can(['two', 'woop'], false) );
	}

}
