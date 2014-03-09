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
                'The ID of the board whose structure you want to get.'
            )
            ->addOption(
                'prova',
                'p',
                InputOption::VALUE_REQUIRED,
                'blabla: '
            )
            ->addOption(
                'short',
                '',
                InputOption::VALUE_NONE,
                'Display a minimal subset of information'
            )
            ->addArgument(
                'filters',
                InputArgument::IS_ARRAY,
                ''
            );

        $this->setRequiredOptions([
            'board' => 'Please enter the board id: ',
            /* 'prova' => 'blabla: ', */
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filters = $input->getArgument('filters');

        $this->kbize = new Application($input->getOption('env'), $this, $output);
        $container = $this->kbize->getContainer();

        $fieldsToDisplay = $this->fieldsToDisplay($container, $input->getOption('short', false));
        $taskCollection = $this->kbize->getAllTasks($input->getOption('board'));

        if (end($filters) == "show") {
            array_pop($filters);
            foreach ($taskCollection->filter($filters) as $task) {
                $this->showTask($task, $output);
                $output->writeln('');
                $output->writeln('');
            }

            return;
        }


        $table = $this->getHelper('alternate-table')
            ->setLayout(TableHelper::LAYOUT_BORDERLESS)
            ->setCellRowContentFormat('%s ')
            ;

        $table
            ->setHeaders($this->headers($fieldsToDisplay))
            ->setRows($this->rows(
                $taskCollection,
                $filters,
                $fieldsToDisplay
            ));

        $table->render($output);
    }

    private function headers(array $fieldsToDisplay)
    {
        $headers = [];
        foreach ($fieldsToDisplay as $field) {
            $headers[] = ucfirst($this->adjustNameField($field, [
                'taskid' => 'ID',
            ]));
        }

        return $headers;
    }

    private function rows($taskCollection, $filters, $fieldsToDisplay)
    {
        $rows = [];

        foreach ($taskCollection->filter($filters) as $task) {
            $row = [];
            foreach ($fieldsToDisplay as $field) {
                $row[] = $task[$field];
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

    private function fieldsToDisplay($container, $short = false)
    {
        $displaySettings = $container->getParameter('display');
        return $displaySettings[$short ? 'tasks.shortlist' : 'tasks.longlist'];
    }

    private function showTask($task, $output)
    {
        $rows = [];
        foreach ($task as $field => $value) {
            if (is_array($value)) {
                continue;
            }
            $rows[] = [$field, $value];
        }

        $table = $this->getHelper('alternate-table')
            ->setLayout(TableHelper::LAYOUT_BORDERLESS)
            ->setCellRowContentFormat('%s ');
            /* ->setCellRowContentFormat('<bg=black>%s </bg=black>'); */
            /* ->setBorderFormat(' ') */
            /* ->setCellHeaderFormat('<options=underscore>%s</options=underscore>'); */

        $table
            ->setHeaders(['Name', 'Value'])
            ->setRows($rows);

        $table->render($output);
    }
}
