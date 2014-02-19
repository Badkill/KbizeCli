<?php
namespace Badkill\KbizeCli\Sdk;

use Badkill\KbizeCli\Http\ClientInterface;
use Badkill\KbizeCli\Http\Response;

class SdkTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = $this->getMock('Badkill\KbizeCli\Http\ClientInterface', array_merge(
            get_class_methods('Badkill\KbizeCli\Http\ClientInterface'),
            get_class_methods('Guzzle\Http\ClientInterface')
        ));

        $this->request = $this->getMock('Badkill\KbizeCli\Http\RequestInterface');
    }

    public function testLoginWithRightCredentialsReturnsAnArray()
    {
        $email = 'user@example.com';
        $password  = 'secretPassword';

        $data = array(
            'email' => $email,
            'username' => 'name.surname',
            'realname' => 'Name Surname',
            'companyname' => 'Company',
            'timezone' => '0:0',
            'apikey' => 'UIcXtWGF1ldjKmSdYi6lP3WN8o1K4hMJQnOLkfTv',
        );

        $this->requestReturnsJson($data);

        $this->client->expects($this->once())
            ->method('post')
            ->with("login/email/" . urlencode($email) . "/pass/$password")
            ->will($this->returnValue($this->request));

        $sdk = new Sdk($this->client);
        $this->assertEquals($data, $sdk->login($email, $password));
    }

    private function requestReturnsJson($jsonData, $httpStatusCode = 200)
    {
        $responseBody = is_array($jsonData) ? json_encode($jsonData) : $jsonData;
        $response = new Response($httpStatusCode, array(), $responseBody);

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));
    }
}
