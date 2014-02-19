<?php
namespace Badkill\KbizeCli\Http;

use Guzzle\Http\ClientInterface as GuzzleClientInterface;
use Guzzle\Plugin\Mock\MockPlugin as GuzzleMockPlugin;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Plugin\Mock\MockPlugin as GuzzleMock;
use Symfony\Component\EventDispatcher\EventDispatcher;

/* class ClientTest extends \PHPUnit_Framework_TestCase */
class ClientTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function setUp()
    {
        $this->rawClient = new GuzzleClient();
        $this->rawClientMock = new GuzzleMock();
    }

    public function testFromConfigReturnsRightClientObjectIfAllRequiredConfigsArePassed()
    {
        $client = Client::fromConfig(array(
            'baseUrl' => 'http://example.com',
            'apikey' => 'jhdbdauwnchudhw'
        ));

        $this->assertTrue($client instanceof Client);
    }

    /**
     * @expectedException Symfony\Component\OptionsResolver\Exception\MissingOptionsException
     */
    public function testFromConfigBubbleUpExceptionIfMissingSomeRequiredParameters()
    {
        $client = Client::fromConfig(array(
        ));

        $this->assertTrue($client instanceof Client);
    }

    public function testNotExistingMethodAreCalledOnHttpClient()
    {
        $rawClient = $this->guzzleClientMock();
        $rawClient->expects($this->once())
            ->method('post')
            ->with('first argument', 'second argument');

        $client = Client::fromConfig(array(
            'baseUrl' => 'http://example.com',
        ), $rawClient);

        $client->post('first argument', 'second argument');
    }

    /**
     * @expectedException Badkill\KbizeCli\Http\Exception\ForbiddenException
     */
    public function test403ResponseCodeBubbleUpAForbiddenException()
    {
        $this->rawClientRespond(403, array(), '{"status":false,"error":"Invalid API Key."}');
        $this->rawClient->addSubscriber($this->rawClientMock);

        $client = Client::fromConfig(array(
            'baseUrl' => 'http://example.com',
        ), $this->rawClient);

        $response = $client->post('first argument')->send();
    }

    /**
     * @expectedException Badkill\KbizeCli\Http\Exception\ServerErrorResponseException
     */
    public function testNot200ResponseCodeBubbleUpARequestException()
    {
        $this->rawClientRespond(500, array(), '{"status":false,"error":"Server Error"}');
        $this->rawClient->addSubscriber($this->rawClientMock);

        $client = Client::fromConfig(array(
            'baseUrl' => 'http://example.com',
        ), $this->rawClient);

        $client->post('first argument')->send();
    }

    private function guzzleClientMock()
    {
        $rawClient = $this->getMock('Guzzle\Http\ClientInterface', array_merge(
            get_class_methods('Guzzle\Http\ClientInterface'),
            //FIXME:! unfortunately guzzle clientinterface have not this method :(
            ['setDefaultOption']
        ));

        //FIXME: is possibile avoid this?
        /* $rawClient->expects($this->once()) */
        /*     ->method('getEventDispatcher') */
        /*     ->will($this->returnValue(new EventDispatcher())); */

        return $rawClient;
    }

    private function rawClientRespond($statusCode, $headers = null, $body = null)
    {
        $this->rawClientMock->addResponse(
            new GuzzleResponse($statusCode, $headers, $body)
        );
    }
}
