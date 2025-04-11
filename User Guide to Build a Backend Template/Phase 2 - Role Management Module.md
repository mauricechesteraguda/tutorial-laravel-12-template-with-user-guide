🎯 Goal:
Set up role management functionality in the Laravel application.

🚀 Step 1: Create RoleController
Run the following command to create the RoleController:
```
php artisan make:controller RoleController
```

📚 Step 2: Define RoleController Methods
Open app/Http/Controllers/RoleController.php and add the following methods:
```php
<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user->role->name !== 'admin') {
                return redirect()->route('home')->with('error', 'Unauthorized access.');
            }
            return $next($request);
        });
    }

    // Show all roles
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    // Show form to create a new role
    public function create()
    {
        return view('roles.create');
    }

    // Store a new role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->input('name'),
        ]);

        Role::create($request->all());
        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    // Show form to edit a role
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    // Update a role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);

        $role->update($request->all());
        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    // Delete a role
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
```

🎨 Step 3: Create Role Views
Create a roles folder inside resources/views/:
```
mkdir resources/views/roles
```

1. Create Index View: resources/views/roles/index.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Roles</h2>
    <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
            <tr>
                <td>{{ $role->name }}</td>
                <td>
                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
```

2. Create Create View: resources/views/roles/create.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Role</h2>
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Role Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Role</button>
    </form>
</div>
@endsection
```

3. Create Edit View: resources/views/roles/edit.blade.php
```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Role</h2>
    <form action="{{ route('roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Role Name:</label>
            <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Role</button>
    </form>
</div>
@endsection
```

🛤️ Step 4: Define Role Routes
Open routes/web.php and add the routes for role management:
```php
use App\Http\Controllers\RoleController;

Route::middleware(['auth'])->group(function () {
Route::resource('roles', RoleController::class);
});
```

## Migration for Adding role_id in User Model

To add a `role_id` field to the `users` table, create a new migration:

```bash
php artisan make:migration add_role_id_to_users_table --table=users
```

Then, in the migration file, add the following code:

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->unsignedBigInteger('role_id')->after('id')->nullable();
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('role_id');
    });
}
```

Finally, run the migration:

```bash
php artisan migrate
```

## Adding the role_id in the $fillable variable at User.php Model

Go to User.php model at models folder of laravel and add role_id in the $fillable list like the one below:

```php
<?php

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

```


## Step to Create InitialAdminSeeder
Before running the database seeder, create the InitialAdminSeeder.php file with the following content:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class InitialAdminSeeder extends Seeder
{
    public function run()
    {
        // Create the admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create the first admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'), // Use a secure password in production
        ])->role()->associate($adminRole)->save();
    }
}
```


Now run the initial admin seeder:
```bash
php artisan db:seed
```


After these steps, final check by loging in the admin@example.com which is the superuser by default at:

http://localhost:8000/login

This guide provides a foundational framework for developing the role management module in the Laravel backend template.