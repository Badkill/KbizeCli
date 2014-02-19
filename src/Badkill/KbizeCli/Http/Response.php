<?php
namespace Badkill\KbizeCli\Http;

use Guzzle\Http\Message\Response as GuzzleResponse;

class Response extends GuzzleResponse
{
    private $rawResponse;

    public static function fromRawResponse(GuzzleResponse $rawResponse)
    {
        $response = new static(
            $rawResponse->getStatusCode(),
            $rawResponse->getHeaders(),
            $rawResponse->getBody()
        );
        /* $this->setRawResponse($rawResponse); */
    }

    /* public function __construct($statusCode, $headers = null, $body = null) */
    /* { */
    /*     parent::__construct($statusCode, $headers, $body); */
    /*     $this->setRawResponse($this); */
    /* } */

    /* public function __call($method, $args) */
    /* { */
    /*     return call_user_func_array(array($this->rawResponse, $method), $args); */
    /* } */

    /* private function setRawResponse(GuzzleResponse $rawResponse) */
    /* { */
    /*     $this->rawResponse = $rawResponse; */
    /* } */

    /* public function __construct(GuzzleResponse $rawResponse) */
    /* { */
    /*     $this->rawResponse = $rawResponse; */
    /* } */
}
