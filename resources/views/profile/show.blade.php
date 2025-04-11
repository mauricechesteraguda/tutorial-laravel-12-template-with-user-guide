@extends('layouts.app')

@section('content')
<div class="container">
	<h2>User Profile</h2>

	@if(session('success'))
    	<div class="alert alert-success">{{ session('success') }}</div>
	@endif

	<form action="{{ route('profile.update') }}" method="POST">
    	@csrf
    	@method('PUT')

    	<div class="form-group mb-3">
        	<label>Name:</label>
        	<input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
    	</div>

    	<div class="form-group mb-3">
        	<label>Email:</label>
        	<input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
    	</div>

    	<button type="submit" class="btn btn-primary">Update Profile</button>
	</form>

	<hr>

	<form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your account?');">
    	@csrf
    	@method('DELETE')
    	<button type="submit" class="btn btn-danger">Delete Account</button>
	</form>
</div>
@endsection
