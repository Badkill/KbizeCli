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
            return new \KbizeCli\Http\Exception\ServerErrorResponseException($e);
        }

        if ($e instanceof ClientErrorResponseException) {
            $response = $e->getResponse();

            if ($response->getStatusCode() == 403) {
                return ForbiddenException::factory($e->getRequest(), $response);
            }

            return new \KbizeCli\Http\Exception\ClientErrorResponseException($e);
        }
    }
}
