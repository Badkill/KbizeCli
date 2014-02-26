<?php
namespace KbizeCli\Cache;

interface Cache
{
    /**
     * Read data from cache storage
     * @return array
     */
    public function read();

    /**
     * Write data onto cache storage
     *
     * @params array $data
     * @return self
     */
    public function write(array $data = [], $level = 2);
}
