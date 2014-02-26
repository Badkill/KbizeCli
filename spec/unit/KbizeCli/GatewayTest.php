<?php
namespace KbizeCli;

use KbizeCli\Sdk\Exception\ForbiddenException;

class GatewayTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sdk = $this->getMock('KbizeCli\Sdk\SdkInterface');
        $this->user = $this->getMock('KbizeCli\UserInterface');
        $this->cache = $this->getMock('KbizeCli\Cache\Cache');
        $this->gw = new Gateway($this->sdk, $this->user, $this->cache, 'cache/path');
    }

    public function testLoginReturnsAnUpdatedUserInstance()
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

        $this->user->expects($this->once())
            ->method('update')
            ->with($data)
            ->will($this->returnValue($this->user));

        $user = $this->gw->login($email, $password);
        $this->assertInstanceOf('KbizeCli\UserInterface', $user);
    }

    /* /** */
    /*  * @expectedException KbizeCli\Sdk\Exception\ForbiddenException */
    /*  *1/ */
    /* public function testInCaseOfSdkExceptionRetryTwoTimesBeforeBubbleUpException() */
    /* { */
    /*     $this->sdk->expects($this->exactly(3)) */
    /*         ->method('getProjectsAndBoards') */
    /*         ->will($this->throwException(new ForbiddenException())); */

    /*     $this->gw->getProjectsAndBoards(); */
    /* } */

    /* public function testCallsLoginInCaseOfForbiddenException() */
    /* { */
    /*     $this->markTestSkipped(); */
    /*     $this->sdk->expects($this->once()) */
    /*         ->method('getProjectsAndBoards') */
    /*         ->will($this->throwException(new ForbiddenException())); */

    /*     $userData = [ */
    /*         'email' => 'name@company.com', */
    /*         'username' => 'name.surname', */
    /*         'realname' => 'Name Surname', */
    /*         'companyname' => 'Company', */
    /*         'timezone' => '0:0', */
    /*         'apikey' => 'UIcXtWGF1ldjKmSdYi6lP3WN8o1K4hMJQnOLkfTv', */
    /*     ]; */

    /*     $this->sdk->expects($this->once()) */
    /*         ->method('login') */
    /*         ->will($this->returnValue($userData)); */

    /*     $this->gw->getProjectsAndBoards(); */
    /* } */

    /* public function testProjectsAndBoards() */
    /* { */
    /*     $data = [ */
    /*         'username' => 'name.surname', */
    /*         'realname' => 'Name Surname', */
    /*         'companyname' => 'Company', */
    /*         'timezone' => '0:0', */
    /*         'apikey' => 'UIcXtWGF1ldjKmSdYi6lP3WN8o1K4hMJQnOLkfTv', */
    /*     ]; */

    /*     $this->sdk->expects($this->once()) */
    /*         ->method('getProjectsAndBoards') */
    /*         ->with() */
    /*         ->will($this->returnValue($data)); */

    /*     $user = $this->gw->getProjectsAndBoards(); */
    /* } */
}
