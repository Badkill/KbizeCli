<?php
namespace KbizeCli;

class User
{
    private $data;

    public function __construct()
    {
    }

    public function update(array $data)
    {
        $newUser = new static();
        $newUser->setData($data);

        return $newUser;
    }

    public function isAuthenticated()
    {
        if (isset($this->data['apikey'])) {
            return true;
        }

        return false;
    }

    public function apikey()
    {
        return $this->data['apikey'];
    }

    public function toArray()
    {
        return $this->data;
    }

    private function ensureIsValidData(array $data)
    {
        //TODO:! Data Validation
    }

    private function setData(array $data)
    {
        $this->ensureIsValidData($data);
        $this->data = $data;
    }
}
