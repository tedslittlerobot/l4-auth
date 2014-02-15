@extends('l4-auth::layout')

@section('content')

	<h1>Users:</h1>

	@foreach( $users as $user )
		<article>
			<a href="{{ route('admin.user', [ 'user_id' => $user->id ])"></a>
				<h1>{{ $user->name }}</h1>
			<a href="{{ route('admin.user.edit', [ 'user_id' => $user->id ]) }}">
				Edit user
			</a>
		</article>
	@endforeach

	{{ $users->links() }}

@stop
