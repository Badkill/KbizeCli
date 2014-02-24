<?php
namespace KbizeCli\Http\Exception;

use Guzzle\Http\Exception\ServerErrorResponseException as GuzzleServerErrorResponseException;

class ServerErrorResponseException extends GuzzleServerErrorResponseException
{
    public static function fromRaw(GuzzleServerErrorResponseException $raw)
    {
        $e = new self($raw->getMessage(), $raw->getCode(), $raw->getPrevious());
        $e->setRaw($raw);

        return $e;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->raw, $method), $args);
    }

    private function setRaw(GuzzleServerErrorResponseException $raw)
    {
        $this->raw = $raw;
    }
}
