<?php
namespace Badkill\KbizeCli\Sdk;

class Url
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public static function fromPath($path)
    {
        return new self($path);
    }

    public function withParams(array $params)
    {
        $this->path .= $this->resolveParams($params);

        return $this;
    }

    private function resolveParams(array $params)
    {
        $paramsPath = "";

        foreach ($params as $key => $value) {
            $paramsPath .= '/' . $key . '/' . urlencode($value);
        }

        return $paramsPath;
    }

    public function __toString()
    {
        return $this->path;
    }
}
