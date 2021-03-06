<?php
namespace KbizeCli;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use KbizeCli\Sdk\ApiInterface;
use KbizeCli\Sdk\Sdk;
use KbizeCli\Sdk\Exception\ForbiddenException;
use KbizeCli\Cache\Cache;
use KbizeCli\TaskCollection;

class Gateway implements KbizeInterface
{
    private $sdk;
    private $user;
    private $apikey;

    public function __construct(ApiInterface $sdk, UserInterface $user, Cache $cache, $cachePath)
    {
        $this->sdk = $sdk;
        $this->user = $user;
        $this->cache = $cache;
        $this->cachePath = $cachePath;

        if ($this->user->isAuthenticated()) {
            $this->sdk->setApikey($this->user->apikey());
        }
    }

    public function isAuthenticated()
    {
        return $this->user->isAuthenticated();
    }

    public function login($email, $password)
    {
        $userData = $this->sdk->login($email, $password);
        $this->user = $this->user->update($userData, true);
        $this->sdk->setApikey($this->user->apikey());

        return $this->user;
    }

    public function getProjects()
    {
        $projects = [];
        foreach ($this->getProjectsAndBoards()['projects'] as $project) {
            $projects[$project['id']] = $project['name'];
        }

        return $projects;
    }

    public function getBoards($projectId)
    {
        $boards = [];
        foreach ($this->getProjectsAndBoards()['projects'] as $project) {
            if ($projectId == $project['id']) {
                foreach ($project['boards'] as $board) {
                    $boards[$board['id']] = $board['name'];
                }
            }
        }

        return $boards;
    }

    public function getAllTasks($boardId, $useCache = true)
    {
        $cacheFile = $useCache ? $boardId . DIRECTORY_SEPARATOR . 'tasks.yml' : null;
        $tasks = $this->callSdkWithCache('getAllTasks', [$boardId], $cacheFile);

        return TaskCollection::box($tasks);
    }

    public function getBoardStructure($boardId, $useCache = true)
    {
        $cacheFile = $useCache ? $boardId . DIRECTORY_SEPARATOR . 'boardStructure.yml' : null;
        $boardStructure = $this->callSdkWithCache('getBoardStructure', [$boardId], $cacheFile);

        return $boardStructure;
    }

    public function getLanes($boardId, $useCache = true)
    {
        $lanes = [];

        $boardStructure = $this->getBoardStructure($boardId, $useCache);

        foreach ($boardStructure['lanes'] as $lane) {
            $lanes[] = $lane['lcname'];
        }

        return $lanes;
    }

    public function getBoardSettings($boardId, $useCache = true)
    {
        $cacheFile = $useCache ? $boardId . DIRECTORY_SEPARATOR . 'boardSettings.yml' : null;
        $boardSettings = $this->callSdkWithCache('getBoardSettings', [$boardId], $cacheFile);

        return $boardSettings;
    }

    public function getUsernames($boardId, $useCache = true)
    {
        $settings = $this->getBoardSettings($boardId, $useCache);

        return $settings['usernames'];
    }

    public function getTypes($boardId, $useCache = true)
    {
        $settings = $this->getBoardSettings($boardId, $useCache);

        return $settings['types'];
    }

    public function getTemplates($boardId, $useCache = true)
    {
        $settings = $this->getBoardSettings($boardId, $useCache);

        return $settings['templates'];
    }

    public function createNewTask($boardId, array $parameters)
    {
        return $this->sdk->createNewTask($boardId, $parameters);
    }

    /* public function moveTask($boardId, array $parameters) */
    public function moveTask($boardId, $taskId, $column = 'Backlog', array $parameters = [])
    {
        return $this->sdk->moveTask($boardId, $taskId, $column, $parameters);
    }

    public function callSdk($method, array $args = [])
    {
        return call_user_func_array([$this->sdk, $method], $args);
    }

    //TODO:! cache should depends on args
    public function callSdkWithCache($method, array $args = [], $cacheFile = null)
    {
        $data = [];

        if ($cacheFile) {
            $data = $this->fromCache($cacheFile);
        }

        if (!$data) {
            $data = $this->callSdk($method, $args);

            if ($cacheFile) {
                $this->cache($cacheFile, $data);
            }
        }

        return $data;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function clearCache()
    {
        $this->cache->clear($this->cachePath);
    }

    private function getProjectsAndBoards($useCache = true)
    {
        if (!isset($this->projectsAndBoards)) {
            $cacheFile = $useCache ? 'projectsAndBoards.yml' : null;
            $this->projectsAndBoards = $this->callSdkWithCache('getProjectsAndBoards', [], $cacheFile);
        }

        return $this->projectsAndBoards;
    }

    private function cache($file, array $data)
    {
        $this->cache->write($this->cachePath . DIRECTORY_SEPARATOR . $file, $data);
    }

    private function fromCache($file)
    {
        return $this->cache->read($this->cachePath . DIRECTORY_SEPARATOR . $file);
    }
}
