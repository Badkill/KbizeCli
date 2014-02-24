<?php
namespace KbizeCli;

use KbizeCli\Sdk\ApiInterface;
use KbizeCli\Sdk\Sdk;
use KbizeCli\Sdk\Exception\ForbiddenException;

class Gateway implements KbizeInterface
{
    private $sdk;
    private $user;
    private $apikey;

    public function __construct(ApiInterface $sdk, User $user, $apikey = "")
    {
        $this->sdk = $sdk;
        $this->user = $user;
        $this->apikey = $apikey;
        if ($this->apikey) {
            $this->sdk->setApikey($this->apikey);
        }
    }

    public function login($email, $password)
    {
        $userData = $this->sdk->login($email, $password);
        $this->user = $this->user->update($userData);
        $this->sdk->setApikey($this->user->apikey());

        return $this->user;
    }

    public function getProjectsAndBoards()
    {
        return $this->callSdk('getProjectsAndBoards');
    }

    public function callSdk($method, array $args = [])
    {
        return call_user_func_array([$this->sdk, $method], $args);
    }
}
