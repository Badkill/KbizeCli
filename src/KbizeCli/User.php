<?php
namespace KbizeCli;

class User
{
    private $data;

    public static function fromData(array $data)
    {
        return new self($data);
    }

    public function __construct(array $data)
    {
        $this->ensureIsValidData($data);
        $this->data = $data;
    }

    public function isAuthenticated()
    {
        if (isset($this->data['apikey'])) {
            return true;
        }

        return false;
    }

    public function toArray()
    {
        return $this->data;
    }

    public function ensureIsValidData(array $data)
    {
        //TODO:! Data Validation
    }
}
