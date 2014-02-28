<?php
namespace KbizeCli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\TableHelper;
use KbizeCli\Application;
use KbizeCli\TaskCollection;
use KbizeCli\Console\Command\BaseCommand;

/**
 *
 */
class TasksCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('tasks')
            ->setDescription('Show a list of tasks')
            ->setHelp('This is the help for the tasks command.')
            ->addOption(
                'env',
                'e',
                InputOption::VALUE_OPTIONAL,
                'set the environment for different configuration',
                'prod'
            )
            ->addOption(
                'board',
                'b',
                InputOption::VALUE_REQUIRED,
                'The ID of the board whose structure you want to get.',
                ''
            )
            ->addArgument(
                'filters',
                InputArgument::IS_ARRAY,
                ''
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //FIXME:!!! should be done in baseCommand in some automatic way....
        $this->output = $output;

        $this->kbize = new Application($input->getOption('env'), $this, $output);

        $container = $this->kbize->getContainer();

        $displayedFields = $this->displayedFields($container);

        $taskCollection = new TaskCollection(
            $this->kbize->getAllTasks($input->getOption('board'))
        );

        $table = $this->getHelperSet()->get('table')
            ->setLayout(TableHelper::LAYOUT_BORDERLESS)
            ->setCellRowContentFormat('%s ');
            /* ->setCellRowContentFormat('<bg=black>%s </bg=black>'); */
            /* ->setBorderFormat(' ') */
            /* ->setCellHeaderFormat('<options=underscore>%s</options=underscore>'); */

        $table
            ->setHeaders($this->headers($displayedFields))
            ->setRows($this->rows(
                $taskCollection,
                $input->getArgument('filters'),
                $displayedFields
            ));

        $table->render($output);
    }

    private function headers(array $displayedFields)
    {
        $headers = [];
        foreach ($displayedFields as $field) {
            $headers[] = ucfirst($this->adjustNameField($field, [
                'taskid' => 'ID',
            ]));
        }

        return $headers;
    }

    private function rows($taskCollection, $filters, $displayedFields)
    {
        $rows = [];

        $colors = ["", "magenta"];
        $alternate = 0;
        foreach ($taskCollection->filter($filters) as $task) {

            $color = $colors[$alternate++%2];

            $row = [];
            foreach ($displayedFields as $field) {
                $row[] = $this->color($task[$field], $color);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    private function color($string, $color = "")
    {
        if ($color) {
            return "<fg=$color>$string</fg=$color>";
        }

        return $string;
    }

    private function adjustNameField($field, array $fixes = [])
    {
        if (array_key_exists($field, $fixes)) {
            return $fixes[$field];
        }

        return $field;
    }

    private function displayedFields($container)
    {
        $displaySettings = $container->getParameter('display');
        return $displaySettings['tasks.longlist'];
    }
}
