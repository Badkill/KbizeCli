<?php
namespace KbizeCli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use KbizeCli\Application;
use KbizeCli\Console\Command\BaseCommand;

/**
 * Just a Test command...
 */
class TestCommand extends BaseCommand
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
        //FIXME:!!! should be done in baseCommand in some automatic way....
        $this->output = $output;

        $output->writeln('start');
        $this->kbize = new Application($input->getOption('env'), $this, $output);
        $res = $this->kbize->getProjectsAndBoards();
        var_export($res);
        /* $this->kbize->login('danilo.silva@neomobile.com', ''); */
    }
}
