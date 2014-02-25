<?php
namespace KbizeCli;
use KbizeCli\Cache;

class User implements UserInterface
{
    private $data;
    private $cache;

    public static function fromCache(Cache $cache)
    {
        $user = new static($cache);
        $data = $cache->read();

        if ($data) {
            $user->setData($data);
        }

        return $user;
    }

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
        $this->data = [];
    }

    public function update(array $data)
    {
        $newUser = new static($this->cache);
        $newUser->setData($data);

        return $newUser;
    }

    public function store()
    {
        $this->cache->write($this->data);

        return $this;
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
