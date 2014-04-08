<?php
namespace KbizeCli\Sdk;

use KbizeCli\Http\ClientInterface;
use KbizeCli\Http\Exception\ServerErrorResponseException;
use KbizeCli\Http\Exception\ClientErrorResponseException;
use KbizeCli\Sdk\Exception\ForbiddenException;

class Sdk implements SdkInterface
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

        try {
            return $this->send($request);
        } catch (ClientErrorResponseException $e) {
            throw new ForbiddenException("Authentication failed\n Email and/or password are wrong");
        }
    }

    public function getProjectsAndBoards()
    {
        $request = $this->post('get_projects_and_boards');
        return $this->send($request);
    }

    public function getBoardStructure($boardId)
    {
        return $this->getFullBoardStructure($boardId);
    }

    public function getFullBoardStructure($boardId)
    {
        $request = $this->post('get_full_board_structure', [
            'boardid' => $boardId,
        ]);

        return $this->send($request);
    }

    public function getBoardSettings($boardId)
    {
        $request = $this->post('get_board_settings', [
            'boardid' => $boardId,
        ]);

        return $this->send($request);
    }

    public function getBoardActivities($boardId, $fromDate, $toDate, array $parameters = array())
    {

    }

    public function createNewTask($boardId, array $parameters = array())
    {
        $request = $this->post('create_new_task', array_merge(
            ['boardid' => $boardId],
            $parameters
        ));

        return $this->send($request);
    }

    public function deleteTask($boardId, $taskId)
    {
        $request = $this->post('delete_task', [
            'boardid' => $boardId,
            'taskid' => $taskId,
        ]);

        return $this->send($request);
    }

    public function getTaskDetails($boardId, $taskId, array $parameters = array())
    {
        $request = $this->post('get_task_details', [
            'boardid' => $boardId,
            'taskid' => $taskId,
        ]);

        return $this->send($request);
    }

    public function getAllTasks($boardId, array $parameters = array())
    {
        $request = $this->post('get_all_tasks', array_merge(
            $parameters,
            ['boardid' => $boardId]
        ));
        return $this->send($request);
    }

    public function addComment($taskId, $comment)
    {

    }

    public function moveTask($boardId, $taskId, $column = 'Backlog', array $parameters = array())
    {
        $request = $this->post('move_task', array_merge(
            ['boardid' => $boardId, 'taskid' => $taskId, 'column' => $column],
            $parameters
        ));

        return $this->send($request);
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
        $this->client->setApikey($apikey);

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
            throw new ForbiddenException('Authentication (apikey) is required!');
        }
    }
}
