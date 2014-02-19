<?php
namespace Badkill\KbizeCli\Http;

use Guzzle\Http\Message\EntityEnclosingRequest as GuzzleEntityEnclosingRequest;
use Badkill\KbizeCli\Http\Exception\HttpException;

class Request implements RequestInterface
{
    private $request;

    public function __construct(GuzzleEntityEnclosingRequest $request)
    {
        $this->request = $request;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->request, $method), $args);
    }

    public function send()
    {
        try {
            $guzzleResponse = $this->request->send();
        } catch (\Exception $e) {
            throw HttpException::from($e);
        }

        return Response::fromRawResponse($guzzleResponse);
    }
}
