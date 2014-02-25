<?php
namespace KbizeCli;

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
    public function write(array $data = []);
}
