<?php
namespace KbizeCli\Sdk;

use KbizeCli\Http\ClientInterface;
use KbizeCli\Http\Exception\ServerErrorResponseException;

class Sdk implements ApiInterface
{
    private $client;
    private $apikey;

    public function __construct(ClientInterface $client, $apikey = "")
    {
        $this->client = $client;
        $this->apikey = $apikey;
    }

    public function login($email, $password)
    {
        $request = $this->post('login', [
            'email' => $email,
            'pass' => $password,
        ], false);

        return $this->send($request);
    }

    public function getProjectsAndBoards()
    {
        $request = $this->post('get_projects_and_boards');
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

    public function setApikey($apikey)
    {
        $this->apikey = $apikey;

        return $this;
    }

    private function post($url, array $data = array(), $needAuth = true)
    {
        return $this->client($needAuth)->post($url, [
            'Content-Type' => 'application/json'
        ], json_encode($data));
    }

    private function send($request)
    {
        $response = $request->send();
        /* $response->ensureIsValid(); */
        $data = $response->json();

        return $data;
    }

    private function client($needAuth = true)
    {
        if ($needAuth) {
            $this->ensureIsValidApikey();
        }

        return $this->client;
    }

    private function ensureIsValidApikey()
    {
        if (!isset($this->apikey) || !$this->apikey) {
            throw new \RuntimeException('Authentication (apikey) is required!'); //TODO: Change excpetion
        }
    }
}
