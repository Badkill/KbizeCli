<?php
namespace Badkill\KbizeCli\Sdk;

use Badkill\KbizeCli\Http\ClientInterface;
use Badkill\KbizeCli\Http\Exception\ServerErrorResponseException;

class Sdk implements ApiInterface
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function login($email, $password)
    {
        $request = $this->client->post('login', [], [
            'email' => $email,
            'pass' => $password,
        ]);

        return $this->send($request);
    }

    public function getProjectsAndBoards()
    {
        $request = $this->client->post('get_projects_and_boards');
        return $this->send($request);
    }

    public function getBoardStructure($boardId)
    {

    }

    public function getFullBoardStructure($boardId)
    {

    }

    public function getBoardSettings($boardId)
    {

    }

    public function getBoardActivities($boardId, $fromDate, $toDate, array $parameters = array())
    {

    }

    public function createNewTask($boardId, array $parameters = array())
    {

    }

    public function deleteTask($boardId, $taskId)
    {

    }

    public function getTaskDetails($boardId, $taskId, array $parameters = array())
    {

    }

    public function getAllTasks($boardId, array $parameters = array())
    {

    }

    public function addComment($taskId, $comment)
    {

    }

    public function moveTask($boardId, $taskId, $column, array $parameters = array())
    {

    }

    public function editTask($boardId, $taskId, array $parameters = array())
    {

    }

    public function blockTask($boardId, $taskId, $event, $blockreason)
    {

    }

    public function addSubtask($taskParent, array $parameters = array())
    {

    }

    public function editSubtask($boardId, $subtaskId, array $parameters = array())
    {

    }

    private function send($request)
    {
        $response = $request->send();
        /* $response->ensureIsValid(); */
        $data = $response->json();

        return $data;
    }

    //FIXME:! wrap the response and inject this method
    /* private function ensureIsValidResponse($request) */
    /* { */
    /*     $isValid = true; */
    /*     $response = $request->send(); */

    /*     try { */
    /*         $data = $response->json(); */
    /*     } catch (\Exception $e) { */
    /*         $isValid = false; */
    /*         $data = array('error' => 'Invalid json in response: `' . $response->getBody() . '`'); */
    /*     } */

    /*     if ($response->isError() || !$isValid) { */
    /*         throw new ServerErrorResponseException($request, $response); */
    /*     } */

    /*     return $data; */
    /* } */
}
