🎯 Goal:
Set up user management functionality in the Laravel application.

🚀 Step 1: Create UserController
Run the following command to create the UserController:
```
php artisan make:controller UserController
```

📚 Step 2: Define UserController Methods
Open app/Http/Controllers/UserController.php and add the following methods:
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
	// Show user profile
	public function show()
	{
		$user = Auth::user();
		return view('profile.show', compact('user'));
	}


	// Show form to add a user
    public function create(User $user)
    {
        return view('users.create', compact('user'));
    }

	// Show form to edit a user
	public function edit(User $user)
	{
		$roles = \App\Models\Role::all(['id', 'name']);
		return view('users.edit', compact('user', 'roles'));
	}

	
	

	public function update(Request $request)
	{
		$loggeduser = Auth::user();

		if ($loggeduser->role && $loggeduser->role->name != 'admin') {
			return redirect()->route('users.index')->with('error', 'You do not have permission to update this user.');
		}

		$user = null; // <-- Add this line

		try {
			$user = User::findOrFail($request->input('id'));

			$request->validate([
				'name' => 'required|string|max:255',
				'email' => [
					'required',
					'email',
					Rule::unique('users')->ignore($user->id),
				],
				'role_id' => 'required|exists:roles,id',
			]);

			$user->update($request->only(['name', 'email', 'role_id']));
			$user->save();

			return redirect()->route('users.index')->with('success', 'Profile updated successfully.');
		} catch (\Exception $e) {
			\Log::error('Error updating user profile: ' . $e->getMessage(), [
				'user_id' => $user?->id ?? 'N/A', // handles case where $user is still null
				'request_data' => $request->all(),
			]);

			return redirect()->route('users.index')->with('error', 'An error occurred while updating the profile.');
		}
	}



	// Delete user account
	public function destroy()
	{
		$user = Auth::user();
		$user->delete();

		Auth::logout();

		return redirect('/')->with('success', 'Account deleted successfully.');
	}

	// Create a new user
	public function store(Request $request)
	{
		$request->validate([
			'name' => 'required|string|max:255',
			'email' => 'required|email|unique:users,email',
			'password' => 'required|string|min:8|confirmed',
			'role_id' => 'required|exists:roles,id',
		]);

		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => bcrypt($request->password),
			'role_id' => $request->role_id,
		]);

		return redirect()->route('users.index')->with('success', 'User created successfully.');
	}

	// List all users
	public function index()
	{
		$users = User::all();
		return view('users.index', compact('users'));
	}
}



```

🎨 Step 3: Create User Views
Create a users folder inside resources/views/:
```
mkdir resources/views/users
```

1. Create Index View: resources/views/users/index.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Users</h2>
    <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        @if($user->role && $user->role->name != 'admin')
                        <button type="submit" class="btn btn-danger">Delete</button>
                        @endif
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
```

2. Create Create View: resources/views/users/create.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create User</h2>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
</div>
@endsection
```

3. Create Edit View: resources/views/users/edit.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit User</h2>
    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
        <input type="hidden" name="id" value="{{ $user->id }}">

            <label for="name">Name:</label>
            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>
        <div class="form-group">
            <label for="role_id">Role:</label>
            <select name="role_id" class="form-control" required>
                <option value="">Select a Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
    </form>
</div>
@endsection
```

🛤️ Step 4: Define User Routes
Open routes/web.php and add the routes for user management:
```php
use App\Http\Controllers\UserController;

Route::resource('users', UserController::class);
```


Now try to view the User Management Module at:

http://localhost:8000/users
---

This guide provides a foundational framework for developing the user management module in the Laravel backend template.