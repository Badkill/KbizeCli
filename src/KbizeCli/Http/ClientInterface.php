<?php
namespace KbizeCli\Http;

use Guzzle\Http\ClientInterface as GuzzleClientInterface;

// Guzzle ClientInterfaceAdapter
interface ClientInterface
{
    public static function fromConfig(array $config, GuzzleClientInterface $rawClient);

    public function setApikey($apikey);
}

