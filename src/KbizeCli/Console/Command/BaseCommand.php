<?php
namespace KbizeCli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use KbizeCli\Questioner;
use KbizeCli\Application;

/**
 * Base command
 */
abstract class BaseCommand extends Command
{
    protected $requiredOptions;

    protected function configure()
    {
        $this->addOption(
            'board',
            'b',
            InputOption::VALUE_REQUIRED,
            'The ID of the board whose structure you want to get.'
        )
        ->addOption(
            'env',
            'e',
            InputOption::VALUE_OPTIONAL,
            'set the environment for different configuration',
            'prod'
        )
        ->addOption(
            'project',
            'p',
            InputOption::VALUE_REQUIRED,
            'The ID of the project'
        );

        $this->setRequiredOptions([
            'project' => [
                'question' => 'Choose a project: ',
                'options' => function () {
                    return $this->kbize->getProjects();
                }
            ],
            'board' => [
                'question' => 'Choose the board id: ',
                'options' => function () {
                    return$this->kbize->getBoards($this->input->getOption('project'));
                }
            ],
        ]);
    }

    /* public function ask($question, $default = '', $hiddenResponse = false) */
    /* { */
    /*     $dialog = $this->getHelperSet()->get('dialog'); */
    /*     $method = $hiddenResponse ? 'askHiddenResponse' : 'ask'; */
    /*     return $dialog->$method( */
    /*         $this->output, */
    /*         $question, */
    /*         $default */
    /*     ); */
    /* } */

    public function error($message)
    {
        $formatter = $this->output->getFormatter();
        $this->output->writeln('<error>' . $message . '</error>');
    }

    public function getOutput()
    {
        return $this->output;
    }

    protected function setRequiredOptions(array $required)
    {
        $this->requireOptions = $required;
    }

    protected function askMissingRequiredOptions(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->requireOptions as $key => $data) {
            $output->writeln('');
            $options = is_callable($data['options']) ? $data['options']() : $data['options'];

            if (!is_null($currentChoice = $input->getOption($key, null))) {
                if (in_array($currentChoice, array_keys($options))) {
                    continue;
                }
                // TODO:! show the error even if the available choices is only one
            }

            $this->input->setOption($key, $this->askForMultipleOptions(
                $data['question'],
                $options,
                function($choice) use ($options, $key) {
                    if (!in_array($choice, array_keys($options))) {
                        throw new \InvalidArgumentException('`' . $choice . '` is an invalid ' . $key);
                    }

                    return $choice;
                }
            ));
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $input->setInteractive(true);
        $this->input = $input;
        $this->output = $output;
        $this->kbize = new Application($input->getOption('env'), $output);
        if (!$this->kbize->isAuthenticated()) {
            $this->login();
        }

        if ($input->getOption('no-cache')) {
            $this->kbize->clearCache(false);
        }

        $this->addFilterInCaseOwnOptionIsPresent($input);
    }

    protected function askForMultipleOptions($question, array $options, callable $validation) //FIXME:! RENAME IT
    {
        $defaultChoice = array_keys($options)[0];

        if (count($options) == 1) {
            return $defaultChoice;
        }

        $outputQuestion = [];
        foreach ($options as $id => $label) {
            $outputQuestion[] = "<comment>$id</comment>: $label\n";
        }
        $outputQuestion[] = "<question>$question</question> [<comment>$defaultChoice</comment>] ";

        $choice = $this->getHelper('dialog')->askAndValidate($this->output, $outputQuestion, $validation, 3, $defaultChoice);

        return $choice;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $this->askMissingRequiredOptions($input, $output);
    }

    protected function login()
    {
        $dialog = $this->getHelper('dialog');

        $password = "";
        $email = $dialog->askAndValidate(
            $this->output,
            'Please insert your Kanbanize email: ',
            function ($email) use ($dialog, $password) {

                $password = $dialog->ask(//HiddenResponse(
                    $this->output,
                    '*************************************************************************************
                    ATTENTION: your password will NOT be saved, will be used a Kanbanize generated token.
                    *************************************************************************************
                    Please insert your password: '
                );

                $user = $this->kbize->login($email, $password);
            }
        );
    }

    private function addFilterInCaseOwnOptionIsPresent(InputInterface $input)
    {
        if ($input->getOption('own')) {
            $filters = $input->getArgument('filters');
            array_unshift($filters, 'assignee=' . $this->kbize->getUser()->username);
            $input->setArgument('filters', $filters);
        }
    }
}
