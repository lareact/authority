<?php


namespace Golly\Authority\Tests;

use Exception;
use Golly\Authority\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Class UserModelTest
 * @package Golly\Authority\Tests
 */
class UserModelTest extends TestCase
{

    /**
     * @var string
     */
    protected $password = '123123';

    /**
     * @return User
     */
    public function testCreate()
    {
        $user = (new User())->create([
            "name" => "test",
            "email" => "test@example.com",
            "phone" => "18912690699",
            'group' => 'mp',
            'password' => Hash::make($this->password)
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);

        return $user;
    }

    /**
     *
     */
    public function testLogin()
    {
        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('POST', '/api/auth/login', [
            'username' => 'test@example.com',
            'password' => $this->password
        ]);
        $response->assertStatus(200);
    }

    /**
     * @depends testCreate
     * @param User $user
     * @throws Exception
     */
    public function testDelete(User $user)
    {
        $user->delete();
        $this->assertDeleted('users', [
            'email' => 'test@example.com'
        ]);
    }
}
