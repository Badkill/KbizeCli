<?php
namespace KbizeCli\Tests\Integration;

use Skel\DependencyInjection\Application;
use KbizeCli\Tests\Integration\InputStream;
use KbizeCli\Console\Helper\AlternateTableHelper;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Tester\CommandTester;

class Cli
{
    private $inputStream;
    private $application;
    private $command;
    private $commandTester;
    private $options = [];
    private $inputs = [];

    public function __construct()
    {
        $this->inputStream = $this->inputStream();
        $this->application = $this->application();
        $this->options['--env'] = 'test';
        $this->options['--no-ansi'] = true;
    }

    public function getInputStream()
    {
        return $this->inputStream;
    }

    public function __destruct()
    {
        $this->closeInputStream();
    }

    public function command($command)
    {
        $this->command = $command;

        return $this;
    }

    public function input($input)
    {
        $this->inputs[] = $input;
        fputs($this->inputStream, $input . PHP_EOL);
        /* rewind($this->inputStream); */
    }

    public function hasData()
    {
        return !feof($this->inputStream);
    }

    public function ensureIsEmpty()
    {
        if (!$this->hasData()) {
            return;
        }

        $data = [];
        while (!feof($this->inputStream)) {
            $data[] = stream_get_line($this->inputStream, 4096, PHP_EOL);
        }

        throw new \RuntimeException(
            'Client has input data that have not been read: ' .
            var_export($data, true)
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

        $cwd = '/dati/progett/workspace_danilo/kbizeCli';

        $input = implode($this->inputs, "\n");
        $options = "";
        foreach ($this->options as $key => $value) {
            $options .= " $key $value";
        }

        $command = "php run.php $this->command $options";
        $process = proc_open($command, $descriptorspec, $pipes, $cwd);

        if (is_resource($process)) {

            fwrite($pipes[0], $input . PHP_EOL);
            fclose($pipes[0]);

            $this->output = stream_get_contents($pipes[1]);
            $this->output .= stream_get_contents($pipes[2]);

        }

        return $this->output;
    }

    public function getDisplay()
    {
        /* return $this->commandTester->getDisplay(); */
        return $this->output;
    }

    private function application()
    {
        $application = new Application('KbizeCli');
        $helperSet = $application->getHelperSet();
        $helperSet->set(new AlternateTableHelper());
        $application->setHelperSet($helperSet);

        return $application;
    }

    private function inputStream()
    {
        $this->registerProtocol();
        $stream = fopen("cli://inputStream", "r+", false);
        stream_set_blocking($stream, true);

        return $stream;
    }

    private function closeInputStream()
    {
        if (is_resource($this->inputStream)) {
            fclose($this->inputStream);
        }
    }

    //FIXME:! Move out
    private function registerProtocol()
    {
        static $isRegistered = false;
        if (!$isRegistered) {
            stream_wrapper_register("cli", "\KbizeCli\Tests\Integration\InputStream") or die("Failed to register protocol");
            $isRegistered = true;
        }
    }
}
