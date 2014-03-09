<?php
namespace KbizeCli\Tests\Integration;

use Skel\DependencyInjection\Application;
use KbizeCli\Tests\Integration\InputStream;
use KbizeCli\Console\Helper\AlternateTableHelper;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Tester\CommandTester;

class Client //FIXME: CLI
{
    private $inputStream;
    private $application;
    private $command;
    private $commandTester;

    public function __construct()
    {
        $this->inputStream = $this->inputStream();
        $this->application = $this->application();

    }

    public function __destruct()
    {
        $this->closeInputStream();
    }

    public function command($command)
    {
        $this->command = $this->application->find($command);

        return $this;
    }

    public function input($input)
    {
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

    public function execute()
    {
        $dialog = $this->command->getHelper('dialog');
        $dialog->setInputStream($this->inputStream);

        $this->commandTester = new CommandTester($this->command);
        $this->commandTester->execute([
            'command' => $this->command->getName(),
                /* '--board' => '2', */
                '--env' => 'test',
        ], [
            'interactive' => true,
        ]);
    }

    public function getDisplay()
    {
        return $this->commandTester->getDisplay();
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
