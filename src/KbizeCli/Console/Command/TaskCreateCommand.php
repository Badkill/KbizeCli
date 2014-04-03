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
use KbizeCli\Console\String;
use KbizeCli\Console\Command\BaseCommand;

/**
 *
 */
class TaskCreateCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this->setName('task:create')
            ->setDescription('Create a new task')
            ->setHelp('This is the help for the task:create command.')
            ->addOption(
                'title',
                '',
                InputOption::VALUE_REQUIRED,
                'Title of the task'
            )

            ->addOption(
                'description',
                '',
                InputOption::VALUE_REQUIRED,
                'Description of the task'
            )
            ->addOption(
                'priority',
                '',
                InputOption::VALUE_REQUIRED,
                'One of the following: Low, Average, High'
            )
            ->addOption(
                'assignee',
                '',
                InputOption::VALUE_REQUIRED,
                'Username of the assignee (must be a valid username)'
            )
            ->addOption(
                'color',
                '',
                InputOption::VALUE_REQUIRED,
                'Any color code (e.g. #34a97b) DO NOT PUT the # sign in front of the code!!!'
            )
            ->addOption(
                'size',
                '',
                InputOption::VALUE_REQUIRED,
                'Size of the task'
            )
            ->addOption(
                'tags',
                '',
                InputOption::VALUE_REQUIRED,
                'Space separated list of tags'
            )
            ->addOption(
                'deadline',
                '',
                InputOption::VALUE_REQUIRED,
                'Dedline in the format: yyyy-mm-dd (e.g. 2011-12-13)'
            )
            ->addOption(
                'extlink',
                '',
                InputOption::VALUE_REQUIRED,
                'A link in the following format: https:\\github.com\philsturgeon. If the parameter is embedded in the request BODY, use a standard link: https://github.com/philsturgeon.'
            )
            ->addOption(
                'type',
                '',
                InputOption::VALUE_REQUIRED,
                'The name of the type you want to set.'
            )
            ->addOption(
                'template',
                '',
                InputOption::VALUE_REQUIRED,
                'The name of the template you want to set. If you specify any property as part of the request, the one specified in the template will be overwritten.'
            )
            ->addOption(
                'lane',
                '',
                InputOption::VALUE_REQUIRED,
                'The name of the swim-lane to move the task into'
            )

            ->addArgument(
                'filters',
                InputArgument::IS_ARRAY,
                ''
            );

        $this->addRequiredOptions([
            'title' => [
                'question' => 'Please insert the `title` of the story: ',
            ],
            'lane' => [
                'question' => 'Choose the `lane` where you want create the story: ',
                'options' => function () {
                    return $this->kbize->getLanes($this->input->getOption('board'));
                },
                'validation' => function ($choice, $options, $optionKey) {
                    return $options[$choice];
                }
            ],
            'description' => [
                'question' => 'Please insert the `description` of the story: ',
            ],
            'assignee' => [
                'question' => 'Choose assignee: ',
                'options' => function () {
                    return $this->kbize->getUsernames($this->input->getOption('board'));
                },
                'validation' => function ($choice, $options, $optionKey) {
                    return $options[$choice];
                }
            ],
            'type' => [
                'question' => 'Choose type: ',
                'options' => function () {
                    return $this->kbize->getTypes($this->input->getOption('board'));
                },
                'validation' => function ($choice, $options, $optionKey) {
                    return $options[$choice];
                }
            ]
        ]);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->container = $this->kbize->getContainer();

        $taskData = $this->kbize->createNewTask($input->getOption('board'), [
            'title' => $input->getOption('title'),
            'description' => $input->getOption('description'),
        ]);

        $output->writeln('<info>Task is created, id is: `' . $taskData['id'] . '`</info>');

        if ($input->getOption('lane')) {
            $this->kbize->moveTask(
                $input->getOption('board'),
                $taskData['id'],
                'Backlog',
                [
                    'lane' => $input->getOption('lane'),
                ]
            );
        }
    }
}
