<?php
namespace KbizeCli;

class GatewayTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sdk = $this->getMock('KbizeCli\Sdk\ApiInterface');
        $this->gw = new Gateway($this->sdk);
    }

    public function testLoginReturnsAnUserInstance()
    {
        $email = 'user@example.com';
        $password  = 'secretPassword';

        $data = [
            'email' => $email,
            'username' => 'name.surname',
            'realname' => 'Name Surname',
            'companyname' => 'Company',
            'timezone' => '0:0',
            'apikey' => 'UIcXtWGF1ldjKmSdYi6lP3WN8o1K4hMJQnOLkfTv',
        ];

        $this->sdk->expects($this->once())
            ->method('login')
            ->with($email, $password)
            ->will($this->returnValue($data));

        $user = $this->gw->login($email, $password);
        $this->assertInstanceOf('KbizeCli\User', $user);
        $this->assertEquals($data, $user->toArray());
    }
}
