<?php
namespace KbizeCli;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testUserWithoutApikeyIsNotAuthenticated()
    {
        $user = new User([
            'email' => 'name@company.com',
            'username' => 'name.surname',
            'realname' => 'Name Surname',
            'companyname' => 'Company',
            'timezone' => '0:0',
        ]);

        $this->assertFalse($user->isAuthenticated());
    }

    public function testUserWithApikeyIsAuthenticated()
    {
        $user = new User([
            'email' => 'name@company.com',
            'username' => 'name.surname',
            'realname' => 'Name Surname',
            'companyname' => 'Company',
            'timezone' => '0:0',
            'apikey' => 'UIcXtWGF1ldjKmSdYi6lP3WN8o1K4hMJQnOLkfTv',
        ]);

        $this->assertTrue($user->isAuthenticated());
    }
}
