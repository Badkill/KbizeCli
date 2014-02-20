<?php
namespace KbizeCli;

use KbizeCli\Sdk\ApiInterface;

class Gateway
{
    private $sdk;

    public function __construct(ApiInterface $sdk)
    {
        $this->sdk = $sdk;
    }

    public function login($email, $password)
    {
        $userData = $this->sdk->login($email, $password);
        $user = User::fromData($userData);
        var_export($user);
        return $user;
    }
}
