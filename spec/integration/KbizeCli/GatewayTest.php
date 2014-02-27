<?php
namespace KbizeCli\Tests\Integration;

use KbizeCli\Gateway;
use KbizeCli\Sdk\Sdk;
use KbizeCli\Http\Client;
use KbizeCli\Cache\Cache;

class GatewayTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->user = $this->getMock('KbizeCli\UserInterface');
        $this->cache = $this->getMock('KbizeCli\Cache\Cache');
    }

    /**
     * @expectedException KbizeCli\Sdk\Exception\ForbiddenException
     */
    public function testCallAnApiWhichRequiresAuthenticationWithoutApikeyTriggersAnException()
    {
        $this->user->expects($this->once())
            ->method('isAuthenticated')
            ->will($this->returnValue(false));

        $this->gw = $this->gw();
        $this->gw->getProjectsAndBoards();
    }

    public function testCallAnApiWhichRequiresAuthenticationWithApikeyWorksRight()
    {
        $this->userIsAuthenticated();
        $this->gw = $this->gw();

        $this->gw->getProjectsAndBoards();
    }

    private function sdk()
    {
        return new Sdk(Client::fromConfig([
            'baseUrl' => 'http://localhost:8000',
        ]));
    }

    private function userIsAuthenticated()
    {
        $this->user->expects($this->once())
            ->method('isAuthenticated')
            ->will($this->returnValue(true));

        $this->user->expects($this->once())
            ->method('apikey')
            ->will($this->returnValue('secret-api-key'));
    }

    private function gw()
    {
        return new Gateway($this->sdk(), $this->user, $this->cache, 'cache/path');
    }
}
