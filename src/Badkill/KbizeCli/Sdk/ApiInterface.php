<?php
namespace Badkill\KbizeCli\Sdk;

interface ApiInterface
{
    /**
     * Get User Data and apikey
     *
     * @param string $email Your email address
     * @param string $password Your email address
     *
     * @return array with user information: [
     *   email       Your email address
     *   username    Your username
     *   realname    Your name
     *   companyname Company name
     *   timezone    Your time zone
     *   apykey      Your API key.
     * ]
     */
    public function login($email, $password);

    public function getProjectsAndBoards();

    public function getBoardStructure($boardId);

    public function getFullBoardStructure($boardId);

    public function getBoardSettings($boardId);

    public function getBoardActivities($boardId, $fromDate, $toDate, array $parameters = array());

    public function createNewTask($boardId, array $parameters = array());

    public function deleteTask($boardId, $taskId);

    public function getTaskDetails($boardId, $taskId, array $parameters = array());

    public function getAllTasks($boardId, array $parameters = array());

    public function addComment($taskId, $comment);

    public function moveTask($boardId, $taskId, $column, array $parameters = array());

    public function editTask($boardId, $taskId, array $parameters = array());

    public function blockTask($boardId, $taskId, $event, $blockreason);

    public function addSubtask($taskParent, array $parameters = array());

    public function editSubtask($boardId, $subtaskId, array $parameters = array());
}
