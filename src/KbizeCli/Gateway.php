<?php
namespace KbizeCli;

use KbizeCli\Sdk\ApiInterface;
use KbizeCli\Sdk\Sdk;
use KbizeCli\Sdk\Exception\ForbiddenException;
use KbizeCli\Cache\Cache;

class Gateway implements KbizeInterface
{
    private $sdk;
    private $user;
    private $apikey;

    public function __construct(ApiInterface $sdk, UserInterface $user, Cache $cache, $cachePath)
    {
        $this->sdk = $sdk;
        $this->user = $user;

        if ($this->user->isAuthenticated()) {
            $this->sdk->setApikey($this->user->apikey());
        }
    }

    public function login($email, $password)
    {
        $userData = $this->sdk->login($email, $password);
        $this->user = $this->user->update($userData, true);
        $this->sdk->setApikey($this->user->apikey());

        return $this->user;
    }

    public function getProjectsAndBoards()
    {
        return $this->callSdk('getProjectsAndBoards');
    }

    public function getAllTasks($boardId)
    {
        return $this->callSdk('getAllTasks', [$boardId]);
    }

    public function callSdk($method, array $args = [])
    {
        return call_user_func_array([$this->sdk, $method], $args);
    }
}
