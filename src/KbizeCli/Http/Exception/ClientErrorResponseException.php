<?php
namespace KbizeCli\Http\Exception;

use Guzzle\Http\Exception\ClientErrorResponseException as GuzzleClientErrorResponseException;

class ClientErrorResponseException extends GuzzleClientErrorResponseException
{
    public static function fromRaw(GuzzleClientErrorResponseException $raw)
    {
        $e = new static($raw->getMessage(), $raw->getCode(), $raw->getPrevious());
        $e->setRaw($raw);

        return $e;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->raw, $method), $args);
    }

    private function setRaw(GuzzleClientErrorResponseException $raw)
    {
        $this->raw = $raw;
    }
}
