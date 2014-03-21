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
use \Symfony\Component\Console\Application as SymfonyApplication;

/**
 *
 */
class TaskBoardCommand extends BaseCommand
{
    const BLOCKED_COLOR = 'red';
    private $maxColumnSize;

    public function __construct($name, SymfonyApplication $app)
    {
        $this->app = $app;
        parent::__construct($name);
    }

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

        $this->maxColumnSize = $this->getMaxColumnSize(count($boardStructure['columns']));

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
            $headers[] = $this->formatString($column['lcname']);
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
                        $color = $task['blocked'] ? self::BLOCKED_COLOR : null;
                        $row[] = $this->formatString(
                            $task['taskid'] . ' ' . $task['title'],
                            $color
                        );
                    } else {
                        $row[] = $this->formatString('');
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

    private function formatString($string, $color = false)
    {
        $string = str_pad(
            substr($string, 0, $this->maxColumnSize),
            $this->maxColumnSize,
            ' '
        );

        if ($color) {
            return "<fg=$color>$string</fg=$color>";
        }

        return $string;
    }

    private function getMaxColumnSize($nColumns)
    {
        $terminalDimensions = $this->app->getTerminalDimensions();
        $terminalWidth = $terminalDimensions[0];
        $separatorDImension = ($nColumns * 3) +1;

        $size = floor(($terminalWidth - $separatorDImension ) / $nColumns);

        echo $terminalWidth . "\n";
        echo $size . "\n";
        return $size;
    }
}
