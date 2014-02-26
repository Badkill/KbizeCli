<?php
namespace KbizeCli\Cache;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;

class YamlCache implements Cache
{
    private $path;
    private $parser;
    private $dumper;

    public function __construct($path, $parser, $dumper)
    {
        $this->path = $path;
        $this->parser = $parser;
        $this->dumper = $dumper;
    }

    public function read()
    {
        if (!file_exists($this->path)) {
            return [];
        }

        return $this->parser->parse(file_get_contents($this->path));
    }

    public function write(array $data = [], $level = 2)
    {
        $this->fsSetup($this->path);
        file_put_contents($this->path, $this->dumper->dump($data, $level));
    }

    private function fsSetup()
    {
        if (!file_exists(dirname($this->path))) {
            mkdir (dirname($this->path), 0700, true);
        }

        //TODO: check if is writable, throw exception if not, etc...
    }
}
