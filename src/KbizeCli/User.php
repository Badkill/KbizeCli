<?php
namespace KbizeCli;
use KbizeCli\Cache\Cache;

class User implements UserInterface
{
    private $data;
    private $cache;
    private $cacheFilePath;
    const cacheFilename = 'user.yml';

    public static function fromCache(Cache $cache, $cachePath)
    {
        $cacheFilePath = $cachePath . DIRECTORY_SEPARATOR . self::cacheFilename;
        $user = new static($cache, $cacheFilePath);
        $data = $cache->read($cacheFilePath);

        if ($data) {
            $user->setData($data);
        }

        return $user;
    }

    public function __construct(Cache $cache, $cacheFilePath)
    {
      $this->cache = $cache;
      $this->cacheFilePath = $cacheFilePath;
        $this->data = [];
    }

    public function update(array $data, $store = false)
    {
        $newUser = new static($this->cache, $this->cacheFilePath);
        $newUser->setData($data);

        if ($store) {
            $newUser->store();
        }

        return $newUser;
    }

    public function store()
    {
        $this->cache->write($this->cacheFilePath, $this->data);

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

    public function __get($key)
    {
        return $this->data[$key];
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
