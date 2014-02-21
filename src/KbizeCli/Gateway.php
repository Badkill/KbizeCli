<?php
namespace KbizeCli;

use KbizeCli\Sdk\ApiInterface;
use KbizeCli\Sdk\Sdk;

class Gateway
{
    private $sdk;
    private $apikey;
    private $user;

    public function __construct(ApiInterface $sdk, $apikey = "")
    {
        $this->sdk = $sdk;
        $this->apikey = $apikey;
        if ($this->apikey) {
            $this->sdk->setApikey($this->apikey);
        }
    }

    public function login($email, $password)
    {
        $userData = $this->sdk->login($email, $password);
        $this->user = User::fromData($userData);
        var_export($this->user);

        return $this->user;
    }

    public function getProjectsAndBoards()
    {
        return $this->sdk->getProjectsAndBoards();
    }
}
