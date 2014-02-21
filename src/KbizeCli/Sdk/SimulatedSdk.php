<?php
namespace KbizeCli\Sdk;

use KbizeCli\Http\ClientInterface;
use KbizeCli\Http\Exception\ServerErrorResponseException;

class SimulatedSdk implements ApiInterface
{
    private $apikey;

    public function login($email, $password)
    {
        return [];
    }

    public function getProjectsAndBoards()
    {
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
}
