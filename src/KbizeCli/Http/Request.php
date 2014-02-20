<?php
namespace KbizeCli\Http;

use Guzzle\Http\Message\EntityEnclosingRequest as GuzzleEntityEnclosingRequest;
use Guzzle\Http\Message\RequestInterface as GuzzleRequestInterface;
use KbizeCli\Http\Exception\HttpException;

class Request extends GuzzleEntityEnclosingRequest implements RequestInterface
{
    private $request;

    public function __construct(GuzzleEntityEnclosingRequest $request)
    {
        $this->request = $request;
    }

    public function __call($method, $args)
    {
        $result = call_user_func_array(array($this->request, $method), $args);
        if ($result instanceof GuzzleEntityEnclosingRequest) {
            return $this;
        }

        return $result;
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
