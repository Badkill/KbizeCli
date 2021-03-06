<?php
namespace KbizeCli\Cache;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class YamlCache implements Cache
{
    private $parser;
    private $dumper;

    public function __construct($parser, $dumper)
    {
        $this->parser = $parser;
        $this->dumper = $dumper;
    }

    public function read($path)
    {
        if (!file_exists($path)) {
            return [];
        }

        return $this->parser->parse(file_get_contents($path));
    }

    public function write($path, array $data = [], $level = 2)
    {
        $this->fsSetup($path);
        file_put_contents($path, $this->dumper->dump($data, $level));
    }

    private function fsSetup($path)
    {
        $folderPath = dirname($path);
        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0700, true);
        }

        //TODO: check if is writable, throw exception if not, etc...
    }

    public function clear($path)
    {
        if (file_exists($path)) {
            $fs = new Filesystem();
            $fs->remove($path);
        }
    }
}
