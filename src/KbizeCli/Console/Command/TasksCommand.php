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
        $taskCollection = new TaskCollection(
            $this->kbize->getAllTasks($input->getOption('board'))
        );

        $filters = $input->getArgument('filters');

        $rows = [];

        $colors = ["", "magenta"];
        $alternate = 0;
        foreach ($taskCollection->filter($filters) as $task) {

            $color = $colors[$alternate++%2];
            $rows[] = [
                $this->color($task['taskid'], $color),
                $this->color($task['assignee'], $color),
                $this->color($task['priority'], $color),
                $this->color($task['lanename'], $color),
                $this->color($task['columnname'], $color),
                $this->color($task['title'], $color),
            ];
        }

        $table = $this->getHelperSet()->get('table')
            ->setLayout(TableHelper::LAYOUT_BORDERLESS)
            /* ->setCellRowContentFormat('<bg=black>%s </bg=black>'); */
            ->setCellRowContentFormat('%s ');
            /* ->setBorderFormat(' ') */
            /* ->setCellHeaderFormat('<options=underscore>%s</options=underscore>'); */

        $table
            ->setHeaders(array('ID', 'Assignee', 'Priority', 'LaneName', 'ColumnName', 'Title'))
            ->setRows($rows);

        $table->render($output);
    }

    private function color($string, $color = "")
    {
        if ($color) {
            return "<fg=$color>$string</fg=$color>";
        }

        return $string;
    }
}
