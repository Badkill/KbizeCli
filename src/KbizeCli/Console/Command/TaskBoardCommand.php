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
class TaskBoardCommand extends BaseCommand
{
    const BLOCKED_COLOR = 'red';

    protected function configure()
    {
        parent::configure();

        $this->setName('task:board')
            ->setDescription('Show a list of tasks')
            ->setHelp('This is the help for the tasks command.')
            ->addOption(
                'no-cache',
                'x',
                InputOption::VALUE_NONE,
                'Do not use cached data'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $boardId = $input->getOption('board');
        $this->container = $this->kbize->getContainer();
        $boardStructure = $this->kbize->getBoardStructure($boardId);
        $taskCollection = $this->kbize->getAllTasks($boardId);
        $this->showBoard($boardStructure, $taskCollection);
    }

    private function showBoard($boardStructure, $taskCollection)
    {
        $table = $this->getHelper('table-with-row-title');

        $table
            ->setHeaders($this->headers($boardStructure))
            ->setRows($this->rows($boardStructure, $taskCollection));

        $table->render($this->output);
    }

    private function headers($boardStructure)
    {
        $headers = [];
        foreach($boardStructure['columns'] as $column) {
            $headers[] = $column['lcname'];
        }

        return $headers;
    }

    private function rows($boardStructure, $taskCollection)
    {
        $organizedTask = $this->organizeTask($boardStructure, $taskCollection);


        $rows = [];
        foreach ($boardStructure['lanes'] as $lane) {
            $laneTasks = $organizedTask[$lane['lcid']];
            $rows[] = ['__LANETITLE__', $lane['lcname']];
            for ($i = 0; $i < 5; $i++) {
                $row = [];
                foreach ($boardStructure['columns'] as $column) {
                    if (isset($laneTasks[$column['path']]) && $laneTasks[$column['path']]) {
                        $task = array_shift($laneTasks[$column['path']]);
                        $row[] = $task['taskid'] . ' ' . substr($task['title'], 0, 20);
                    } else {
                        $row[] = '';
                    }
                }
                $rows[] = $row;
            }
        }

        return $rows;
    }

    private function organizeTask($boardStructure, $taskCollection)
    {
        $organized = [];
        foreach ($boardStructure['lanes'] as $lane) {
            $organized[$lane['lcid']] = [];
            foreach ($boardStructure['columns'] as $column) {
                $organized[$lane['lcid']][$column['path']] = [];
            }
        }

        $tasks = $taskCollection->filter([]);
        foreach ($tasks as $task) {
            $organized[$task['laneid']][$task['columnid']][] = $task;
        }

        return $organized;
    }

    private function color($string, $color = "")
    {
        if ($color) {
            return "<fg=$color>$string</fg=$color>";
        }

        return $string;
    }
}
