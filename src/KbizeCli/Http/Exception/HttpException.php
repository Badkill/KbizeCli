<?php
namespace KbizeCli\Http\Exception;

use Guzzle\Http\Exception\HttpException as GuzzleHttpException;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\Message\Response;

abstract class HttpException
{
    public static function from(GuzzleHttpException $e)
    {
        if ($e instanceof ServerErrorResponseException) {
            return \KbizeCli\Http\Exception\ServerErrorResponseException::fromRaw($e);
        }

        if ($e instanceof ClientErrorResponseException) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 403) {
                return ForbiddenException::fromRaw($e);
            }

            return \KbizeCli\Http\Exception\ClientErrorResponseException::fromRaw($e);
        }

        return $e;
    }
}
