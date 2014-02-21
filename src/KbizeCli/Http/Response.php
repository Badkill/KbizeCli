<?php
namespace KbizeCli\Http;

use Guzzle\Http\Message\Response as GuzzleResponse;

class Response extends GuzzleResponse
{
    private $rawResponse;

    public static function fromRawResponse(GuzzleResponse $rawResponse)
    {
        return new static(
            $rawResponse->getStatusCode(),
            $rawResponse->getHeaders(),
            $rawResponse->getBody()
        );
    }

    public function json()
    {
        try {
            $data = parent::json();
        } catch (\Exception $e) {
            //TODO:! throw better exception
            throw new \RuntimeException(
                "Body isn't a valid json: `{$this->body}`\n" . $e->getMessage()
            );
        }

        return $data;
    }
}
