🎯 Goal:
Set up a Laravel application and create a UserController to handle user profile actions, along with comprehensive user management and role management modules.

🚀 Step 1: Install Laravel
Open your terminal and run the following command to create a new Laravel project:

composer create-project laravel/laravel backend

👉 Navigate to the project directory:

cd backend

⚡️ Step 2: Set Up Database
Open .env file in the project root and update the database credentials:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=testdb
DB_USERNAME=testuser
DB_PASSWORD=testpass

2. Create the database in MySQL:

CREATE DATABASE user_profile_app;

3. Run the migrations to set up the default tables:

php artisan migrate

🔐 Step 3: Install Authentication System
If authentication is not yet installed, use the following commands:
composer require laravel/ui

Generate Auth Scaffolding (Bootstrap):

php artisan ui bootstrap --auth

Install Node Dependencies:

npm install && npm run dev

Run the Server:

php artisan serve

Visit:

http://127.0.0.1:8000

🎨 Step 4: Set Up User Authentication
To enable registration, login, and user authentication:
Register a new account at http://127.0.0.1:8000/register

Login at http://127.0.0.1:8000/login

📚 Step 5: Create the UserController
Run this command to create the UserController:
php artisan make:controller UserController

📚 Step 6: Define Controller Methods
Open app/Http/Controllers/UserController.php and add the following methods:
```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
	// Show user profile
	public function show()
	{
    	$user = Auth::user();
    	return view('profile.show', compact('user'));
	}

	// Update user profile
	public function update(Request $request)
	{
    	$user = Auth::user();

    	$request->validate([
        	'name' => 'required|string|max:255',
        	'email' => 'required|email|unique:users,email,' . $user->id,
    	]);

    	$user->update($request->all());

    	return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
	}

	// Delete user account
	public function destroy()
	{
    	$user = Auth::user();
    	$user->delete();

    	Auth::logout();

    	return redirect('/')->with('success', 'Account deleted successfully.');
	}
}
```

🛤️ Step 7: Define Profile Routes
Open routes/web.php and add the routes for profile management:
```
use App\Http\Controllers\UserController;

// Group routes with auth middleware
Route::middleware(['auth'])->group(function () {
	Route::get('/profile', [UserController::class, 'show'])->name('profile.show');
	Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [UserController::class, 'destroy'])->name('profile.destroy');
});
```

✅ Routes Summary:
GET /profile → Show profile

PUT /profile → Update profile

DELETE /profile → Delete account

📚 Step 8: Create Profile Views
Create a profile folder inside resources/views/:
mkdir resources/views/profile

1. Create Show Profile Page: resources/views/profile/show.blade.php

```
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
```

✅ Step 9: Check Routes
Open routes/web.php and ensure that the default auth routes are loaded. The following line should be present:
Auth::routes();

If it’s missing, add it at the bottom of routes/web.php:
```php
use Illuminate\Support\Facades\Auth;

Auth::routes();
Route::middleware(['auth'])->group(function () {	
Route::get('/home', function () {
    return view('home');
})->name('home');
});
```
This will register the default routes:
/register

/login

/logout

/password/reset

✅ Step 10: Clear Route Cache
Clear any existing route cache that might be causing conflicts:
php artisan route:clear

php artisan route:list

You should see routes like:

GET|HEAD   /register ........................ register
GET|HEAD   /login ........................... login

✅ Step 11: Run Migrations Again
If the migration for the users table wasn’t run, the registration page will not work. Run:
php artisan migrate

✅ Step 12: Check Blade Templates
Ensure the login and register views exist in resources/views/auth:
resources/views/auth/login.blade.php

resources/views/auth/register.blade.php

🚀 Final Check:
Visit:
http://127.0.0.1:8000/register ✅

http://127.0.0.1:8000/login ✅

---

### Phase 2: User Management and Role Management Modules

#### Step 1: Create Role Model and Migration
Run the following command to create a Role model and migration:
```
php artisan make:model Role -m
```
Then, open `app/Models/Role.php` and define the model as follows:  
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];
}
```

#### Step 2: Define Role Migration
Open the migration file in `database/migrations/` and define the roles table:
```php
public function up()
{
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->timestamps();
    });
}
```

#### Step 3: Create AdminSeeder
Create a seeder for the roles:
Run the following command:
```
php artisan make:seeder AdminSeeder
```

Open the `database/seeders/AdminSeeder.php` and add the following code:
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }
}
```

#### Step 4: Run the Seeder
Run the seeder to populate the roles table:
```
php artisan db:seed --class=AdminSeeder
```

#### Step 5: Assign Roles to Users
In the User model, define a relationship to the Role model:
```php
public function role()
{
    return $this->belongsTo(Role::class);
}
```

#### Step 6: Update User Registration
Update the registration process to assign a default role to new users.

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign the default role
        $user->role()->associate(Role::where('name', 'user')->first());
        $user->save();

        return $user;
    }
}


```

---

This guide provides a foundational framework for developing a Laravel backend template with user management and role management modules.
