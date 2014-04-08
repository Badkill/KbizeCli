<?php
namespace KbizeCli\Sdk;

use KbizeCli\Http\ClientInterface;
use KbizeCli\Http\Response;
use KbizeCli\Http\Exception\ClientErrorResponseException;

class SdkTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        //FIXME:!
        $this->client = $this->getMock('KbizeCli\Http\ClientInterface', array_merge(
            get_class_methods('KbizeCli\Http\ClientInterface'),
            get_class_methods('Guzzle\Http\ClientInterface')
        ));

        $this->request = $this->getMock('KbizeCli\Http\RequestInterface');
        $this->apikey = 'secret-api-key';
    }

    /**
     * @expectedException \KbizeCli\Sdk\Exception\ForbiddenException
     */
    public function testEveryExceptionsOnLoginMethodAreConvertedIntoForbiddenException()
    {
        $email = 'user@example.com';
        $password  = 'secretPassword';

        $this->request->expects($this->once())
            ->method('send')
            ->will($this->throwException(new ClientErrorResponseException()));

        $this->clientExpectation('login', [
            'email' => $email,
            'pass' => $password,
        ], $this->request);

        $sdk = new Sdk($this->client);
        $sdk->login($email, $password);
    }

    /**
     * @dataProvider methodProvider
     * @expectedException \RuntimeException
     */
    public function testHtmlResponseTriggersAnException(
        $method,
        $arguments,
        $expectedUrl,
        $expectedWith
    )
    {
        $this->requestReturnsJson('<html>test</html>', 200);
        $this->clientExpectation($expectedUrl, $expectedWith, $this->request);

        $sdk = new Sdk($this->client);
        $sdk->setApikey($this->apikey);
        call_user_func_array([$sdk, $method], $arguments);
    }

    /**
     * @dataProvider methodProvider
     * @expectedException \RuntimeException
     */
    public function testExceptionFromClientIsBubbleUpAnException(
        $method,
        $arguments,
        $expectedUrl,
        $expectedWith
    )
    {
        $this->requestReturnsException(new \RuntimeException());
        $this->clientExpectation($expectedUrl, $expectedWith, $this->request);

        $sdk = new Sdk($this->client);
        $sdk->setApikey($this->apikey);
        call_user_func_array([$sdk, $method], $arguments);
    }

    /**
     * @expectedException \KbizeCli\Sdk\Exception\ForbiddenException
     */
    public function testCallAnApiThatRequireAuthenticationTriggerAnExceptionIfApikeyIsMissing()
    {
        $sdk = new Sdk($this->client);
        $sdk->getProjectsAndBoards();
    }

    /**
     * @dataProvider methodProvider
     */
    public function testAllMethodsReturnsDataReturnedFromApiResponse(
        $method,
        $arguments,
        $expectedUrl,
        $expectedWith
    )
    {
        $responseData = [
            'foo' => 'bar'
        ];

        $this->requestReturnsJson($responseData);

        $this->clientExpectation($expectedUrl, $expectedWith, $this->request);


        $sdk = new Sdk($this->client);
        $sdk->setApikey($this->apikey);
        $this->assertEquals(
            $responseData,
            call_user_func_array([$sdk, $method], $arguments)
        );
    }

    public function methodProvider()
    {
        return [
            [
                'getFullBoardStructure',
                [42],
                'get_full_board_structure',
                ['boardid' => 42]
            ],
            [
                'login',
                ['email@email.com', 'secret'],
                'login',
                ['email' => 'email@email.com', 'pass' => 'secret']
            ],
            [
                'getProjectsAndBoards',
                [],
                'get_projects_and_boards',
                []
            ],
            [
                'getBoardStructure',
                [42],
                'get_full_board_structure',
                ['boardid' => 42]
            ],
            [
                'getFullBoardStructure',
                [42],
                'get_full_board_structure',
                ['boardid' => 42]
            ],
            [
                'getBoardSettings',
                [42],
                'get_board_settings',
                ['boardid' => 42]
            ],
            [
                'createNewTask',
                [42, [
                    'title' => 'fake title',
                    'description' => 'fake description',
                ]],
                'create_new_task',
                ['boardid' => 42, 'title' => 'fake title', 'description' => 'fake description']
            ],
            [
                'deleteTask',
                [42, 101],
                'delete_task',
                ['boardid' => 42, 'taskid' => 101]
            ],
            [
                'getAllTasks',
                [42, ['foo' => 'bar']],
                'get_all_tasks',
                ['foo' => 'bar', 'boardid' => 42]
            ],
            [
                'getTaskDetails',
                [42, 101],
                'get_task_details',
                ['boardid' => 42, 'taskid' => 101]
            ],
            [
                'moveTask',
                [42, 101, 'foo', ['lane' => 'bar']],
                'move_task',
                ['boardid' => 42, 'taskid' => 101, 'column' => 'foo', 'lane' => 'bar']
            ],
        ];
    }

    private function clientExpectation($url, $data)
    {
        $this->client->expects($this->once())
            ->method('post')
            ->with($url, ['Content-Type' => 'application/json'], json_encode($data))
            ->will($this->returnValue($this->request));
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
