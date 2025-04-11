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
		$roles = \App\Models\Role::all(['id', 'name']);
        return view('users.create', compact('user', 'roles'));
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
	public function destroy($id)
	{
		$loggedUser = Auth::user();

		if ($loggedUser->role && $loggedUser->role->name != 'admin') {
			return redirect()->route('users.index')->with('error', 'You do not have permission to delete this user.');
		}

		try {
			$user = User::findOrFail($id);
			$user->delete();

			return redirect()->route('users.index')->with('success', 'User deleted successfully.');
		} catch (\Exception $e) {
			\Log::error('Error deleting user: ' . $e->getMessage(), [
				'user_id' => $id,
			]);

			return redirect()->route('users.index')->with('error', 'An error occurred while deleting the user.');
		}
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
