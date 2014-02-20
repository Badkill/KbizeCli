<?php
namespace KbizeCli\Sdk;

use KbizeCli\Http\ClientInterface;
use KbizeCli\Http\Response;

class SdkTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = $this->getMock('KbizeCli\Http\ClientInterface', array_merge(
            get_class_methods('KbizeCli\Http\ClientInterface'),
            get_class_methods('Guzzle\Http\ClientInterface')
        ));

        $this->request = $this->getMock('KbizeCli\Http\RequestInterface');
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
            ->with('login', array(), [
                'email' => $email,
                'pass' => $password,
            ])
            ->will($this->returnValue($this->request));

        $sdk = new Sdk($this->client);
        $this->assertEquals($data, $sdk->login($email, $password));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testHtmlResponseTriggersAnException()
    {
        $email = 'user@example.com';
        $password  = 'secretPassword';

        $this->requestReturnsJson('<html>test</html>', 200);

        $this->client->expects($this->once())
            ->method('post')
            ->with('login', array(), [
                'email' => $email,
                'pass' => $password,
            ])
            ->will($this->returnValue($this->request));

        $sdk = new Sdk($this->client);
        $sdk->login($email, $password);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionFromClientIsBubbleUpAnException()
    {
        $email = 'wrong@email.com';
        $password  = 'wrongPassword';

        $this->requestReturnsException(new \RuntimeException());

        $this->client->expects($this->once())
            ->method('post')
            ->will($this->returnValue($this->request));

        $sdk = new Sdk($this->client);
        $sdk->login($email, $password);
    }

    public function testGetProjectsAndBoards()
    {
        $data = array(
            'projects' => array (
                0 => array (
                    'name' => 'Onebip',
                    'id' => '1',
                    'boards' => array (
                        0 => array (
                            'name' => 'Main development',
                            'id' => '2',
                        ),
                    ),
                ),
            ),
        );

        $this->requestReturnsJson($data);

        $this->client->expects($this->once())
            ->method('post')
            ->with('get_projects_and_boards')
            ->will($this->returnValue($this->request));

        $kbize = new Sdk($this->client);
        $this->assertEquals($data, $kbize->getProjectsAndBoards());
    }

    private function requestReturnsJson($jsonData, $httpStatusCode = 200)
    {
        $responseBody = is_array($jsonData) ? json_encode($jsonData) : $jsonData;
        $response = new Response($httpStatusCode, array(), $responseBody);

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));
    }

    private function requestReturnsException(\Exception $e)
    {
        $this->request->expects($this->once())
            ->method('send')
            ->will($this->throwException($e));
    }
}
