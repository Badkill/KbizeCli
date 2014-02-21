<?php
namespace KbizeCli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use KbizeCli\Kernel;

/**
 * Just a Test command...
 */
class TestCommand extends Command
{
    protected function configure()
    {
        $this->setName('test')
            ->setDescription('Test command')
            ->setHelp('This is the help for the test command.')
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_OPTIONAL,
                'set the environment for different configuration',
                'prod'
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('start');
        $this->kernel = new Kernel($input->getOption('env'));
        $this->kernel->login('danilo.silva@neomobile.com', 'asdRTYjkl123');
    }
}
