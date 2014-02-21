<?php
namespace KbizeCli\Tests\Integration;

use KbizeCli\Gateway;
use KbizeCli\Sdk\Sdk;
use KbizeCli\Http\Client;

class GatewayTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCallAnApiWhichRequiresAuthenticationWithoutApikeyTriggersAnException()
    {
        $this->gw = new Gateway($this->sdk());
        $this->gw->getProjectsAndBoards();
    }

    public function testCallAnApiWhichRequiresAuthenticationWithApikeyWorksRight()
    {
        $this->gw = new Gateway($this->sdk(), 'secret-api-key');
        $this->gw->getProjectsAndBoards();
    }

    private function sdk()
    {
        return new Sdk(Client::fromConfig([
            'baseUrl' => 'http://localhost:8000',
        ]));
    }
}
