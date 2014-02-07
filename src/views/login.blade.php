@extends('l4-auth::layout')

@section('content')

	{{ Form::open( [ 'route' => 'login' ] ) }}

		<fieldset>

			<legend>Log In</legend>

			@if ( $errors->first() )
					<div class="danger alert">{{ $errors->first() }}</div>
			@endif

			<div class="field">
				<input type="text" class="input" placeholder="Email Address">
			</div>

			<div class="field">
				<input type="password" class="input" placeholder="Password">
			</div>


			<div class="medium primary btn">
				{{ Form::submit() }}
			</div>

		</fieldset>

	{{ Form::close() }}

@stop
