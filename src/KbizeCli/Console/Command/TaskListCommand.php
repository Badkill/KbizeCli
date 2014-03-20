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
class TaskListCommand extends BaseCommand
{
    /* const BLOCKED_COLOR = "\e[31m"; */
    const BLOCKED_COLOR = 'red';

    protected function configure()
    {
        parent::configure();

        $this->setName('task:list')
            ->setDescription('Show a list of tasks')
            ->setHelp('This is the help for the tasks command.')
            ->addOption(
                'short',
                '',
                InputOption::VALUE_NONE,
                'Display a minimal subset of information'
            )
            ->addOption(
                'own',
                'o',
                InputOption::VALUE_NONE,
                'Display only my own tasks'
            )
            ->addOption(
                'no-cache',
                'x',
                InputOption::VALUE_NONE,
                'Do not use cached data'
            )
            ->addArgument(
                'filters',
                InputArgument::IS_ARRAY,
                ''
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->kbize->getContainer();

        $filters = $input->getArgument('filters');

        $taskCollection = $this->kbize->getAllTasks($input->getOption('board'));

        if (end($filters) == "show") {
            $this->showTasks($taskCollection, $filters);
        } else {
            $this->showList($taskCollection, $filters, $input);
        }
    }

    private function showTasks($taskCollection, array $filters)
    {
        array_pop($filters);
        foreach ($taskCollection->filter($filters) as $task) {
            $this->showTask($task, $this->output);
            $this->output->writeln('');
            $this->output->writeln('');
        }
    }

    private function showList($taskCollection, array $filters)
    {
        $fieldsToDisplay = $this->fieldsToDisplay(
            $this->container,
            $this->input
        );

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

        $table->render($this->output);
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
            $color = $task['blocked'] ? self::BLOCKED_COLOR : '';
            $row = [];
            foreach ($fieldsToDisplay as $field) {
                $row[] = $this->color($task[$field], $color);
            }

            $rows[] = $row;
        }

        return $rows;
    }

    private function color($string, $color = "")
    {
        if ($color) {
            return "<fg=$color>$string</fg=$color>"; //fgcColor reset all style attributes
            /* return "{$color}{$string}\e[39m"; */
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

    private function fieldsToDisplay($container, $input)
    {
        $short = $input->getOption('short', false);

        $displaySettings = $container->getParameter('display');
        return $displaySettings[$short ? 'tasks.shortlist' : 'tasks.longlist'];
    }

    private function showTask($task, $output)
    {
        $rows = [];
        foreach ($task as $field => $value) {
            if (is_array($value)) {
                //TODO:!!
                continue;
            }

            $color = strstr($field, 'blocked') !== false && $value ? self::BLOCKED_COLOR : '';
            $rows[] = [$field, $this->color($value, $color)];
        }

        $table = $this->getHelper('alternate-table')
            ->setLayout(TableHelper::LAYOUT_BORDERLESS)
            ->setCellRowContentFormat('%s ');

        $table
            ->setHeaders(['Name', 'Value'])
            ->setRows($rows);

        $table->render($output);
    }
}
