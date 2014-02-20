<?php
namespace KbizeCli\Http\Exception;

use Guzzle\Http\Exception\ServerErrorResponseException as GuzzleServerErrorResponseException;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

class ServerErrorResponseException extends GuzzleServerErrorResponseException
{
    public function __construct(GuzzleServerErrorResponseException $e)
    {
        $this->e = $e;
    }

    public static function factory(RequestInterface $request, Response $response)
    {
        $e = parent::factory($request, $response);
        return new static($e);
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->e, $method), $args);
    }
}
