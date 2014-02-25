<?php
namespace KbizeCli;
use KbizeCli\Cache\Cache;

interface UserInterface
{
    public static function fromCache(Cache $cache);

    /**
     * Create new User object and set data on it
     *
     * @params array $data
     * @return new User
     */
    public function update(array $data);

    /**
     * Save data on cache
     *
     * @return User
     */
    public function store();

    /**
     * Chek if User contains apikey
     *
     * @return bool
     */
    public function isAuthenticated();

    public function apikey();

    public function toArray();
}
