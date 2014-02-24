<?php
namespace KbizeCli\Http\Exception;

use KbizeCli\Exception\KbizeCliException;

class ServerErrorResponseException extends KbizeCliException
{
    public static function fromRaw($raw)
    {
        return new static($raw->getMessage(), $raw->getCode(), $raw);
    }
}
