<?php
namespace KbizeCli\Cache;

interface Cache
{
    /**
     * Read data from cache storage
     * @return array
     */
    public function read($path);

    /**
     * Write data onto cache storage
     *
     * @params array $data
     * @return self
     */
    public function write($path, array $data = [], $level = 2);
}
