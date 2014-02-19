<?php
namespace Badkill\KbizeCli\Sdk;

interface ApiInterface
{
    public function login($email, $password);

    public function getProjectsAndBoards();

    public function getBoardStructure($boardId);

    public function getAllTasks($boardId);

    public function getTaskDetails($boardId, $taskId);
}
