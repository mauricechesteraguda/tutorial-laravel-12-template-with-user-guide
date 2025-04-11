🎯 Goal:
Implement unit testing for the RegisterController in the Laravel application.

🚀 Step 1: Create RegisterControllerTest
Run the following command to create the test case:
```
php artisan make:test RegisterControllerTest --unit
```

📚 Step 2: Define Test Methods
Open tests/Feature/RegisterControllerTest.php and add the following methods:
```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role; // Importing the Role model
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a default role for testing
        Role::create(['name' => 'user']);
    }

    /** @test */
    public function it_registers_a_user_with_valid_data()
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/home');
        $this->assertCount(1, User::all());
        $this->assertTrue(Hash::check('password', User::first()->password));
    }

    /** @test */
    public function it_fails_registration_with_invalid_data()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertCount(0, User::all());
    }

    /** @test */
    public function it_fails_registration_with_duplicate_email()
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertCount(1, User::all());
    }
}
```

🛤️ Step 3: Run the Tests
To execute the tests, run the following command:
```
php artisan test
```

This guide provides a foundational framework for implementing unit testing for the RegisterController in the Laravel backend template.
