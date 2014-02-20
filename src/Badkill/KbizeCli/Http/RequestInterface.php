<?php
namespace Badkill\KbizeCli\Http;

use Guzzle\Http\Message\RequestInterface as GuzzleRequestInterface;

interface RequestInterface extends GuzzleRequestInterface
{
    public function send();
}
