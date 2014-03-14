<?php
namespace KbizeCli\Tests\Integration;

use Skel\DependencyInjection\Application;
use KbizeCli\Console\Helper\AlternateTableHelper;
use Symfony\Component\Console\Helper\DialogHelper;

class Cli
{
    private $command;
    private $options = [];
    private $inputs = [];

    public function __construct()
    {
        $this->options['--env'] = 'test';
        $this->options['--no-ansi'] = true;
    }

    public function __destruct()
    {
    }

    public function command($command)
    {
        $this->command = $command;

        return $this;
    }

    public function input($input)
    {
        $this->inputs[] = $input;
    }

    public function hasData()
    {
        return count($this->inputs);
    }

    public function ensureIsEmpty()
    {
        if (!$this->hasData()) {
            return;
        }

        throw new \RuntimeException(
            'Client has input data that have not been read: ' .
            var_export($this->inputs, true)
        );
    }

    public function addOption($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function execute()
    {
        $descriptorspec = array(
            0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
            1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
            2 => array("pipe", "w") // stderr is a file to write to
        );

        $input = implode($this->inputs, "\n");
        $options = "";
        foreach ($this->options as $key => $value) {
            $options .= " $key $value";
        }

        $command = "php run.php $this->command $options";
        $process = proc_open($command, $descriptorspec, $pipes);

        if (is_resource($process)) {

            fwrite($pipes[0], $input . PHP_EOL);
            fclose($pipes[0]);

            $this->output = stream_get_contents($pipes[1]);
            $this->output .= stream_get_contents($pipes[2]);

            fclose($pipes[1]);
            fclose($pipes[2]);

            $returnValue = proc_close($process);
        }

        return $this->output;
    }

    public function getDisplay()
    {
        return $this->output;
    }
}
