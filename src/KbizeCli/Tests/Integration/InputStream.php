<?php
namespace KbizeCli\Tests\Integration;

class InputStream {
    var $position;
    var $varname;
    private $data = [];

    function __call($method, $args)
    {
        echo "**************************\n";
        echo "method => " . $method . "\n";
        echo "**************************\n";
        return true;
    }

    function stream_open($path, $mode, $options, &$opened_path)
    {
        $url = parse_url($path);
        $this->varname = $url["host"];
        $this->position = 0;

        return true;
    }

    function stream_read($count)
    {
        $el = array_shift($this->data);
        $this->position -= 1;
        if ($el) {
            return $el;
        }

        return '';
    }

    function stream_write($data)
    {
        $this->data[] = $data;
        $this->position = count($this->data);
        return strlen($data);
    }

    function stream_tell()
    {
        return $this->position;
    }

    function stream_stat()
    {
        return [
            'size' => count($this->data),
        ];
    }

    function stream_eof()
    {
        error_log('-----------------');
        error_log(var_export($this->data, true));
        $eof = !count($this->data);
        error_log(var_export($eof, true));
        return $eof;
    }

    function stream_seek($offset, $whence)
    {
        switch ($whence) {
        case SEEK_SET:

            if ($offset < count($this->data)) {
                reset($this->data);
                for ($i = 0; $i < $offset; $i++) {
                    next($this->data);
                }

                $this->position = $offset;
                return true;
            }

            return false;
            break;

        case SEEK_CUR:
            if ($offset >= 0) {
                $this->position += $offset;
                return true;
            } else {
                return false;
            }
            break;

        case SEEK_END:
            if (count($this->data)) {
                end($this->data);
                $this->position = count($this->data);
                return true;
            } else {
                return false;
            }
            break;

        default:
            return false;
        }
    }

    function stream_flush()
    {

    }

    function stream_close()
    {

    }
}
