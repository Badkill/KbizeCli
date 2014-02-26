<?php
namespace KbizeCli;
use KbizeCli\Cache\Cache;

class User implements UserInterface
{
    private $data;
    private $cache;
    const cacheFilename = 'user.yml';

    public static function fromCache(Cache $cache, $cachePath)
    {
        $user = new static($cache);
        $data = $cache->read($cachePath . DIRECTORY_SEPARATOR . self::cacheFilename);

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

    public function update(array $data, $store = false)
    {
        $newUser = new static($this->cache);
        $newUser->setData($data);

        if ($store) {
            $newUser->store();
        }

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
