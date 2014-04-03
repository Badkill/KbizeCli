<?php
namespace KbizeCli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use KbizeCli\Questioner;
use KbizeCli\Application;
use \Symfony\Component\Console\Application as SymfonyApplication;

/**
 * Base command
 */
abstract class BaseCommand extends Command
{
    protected $requiredOptions;
    protected $isInteractive = true;

    public function __construct($name, SymfonyApplication $app)
    {
        $this->app = $app;
        $this->requireOptions = [];
        parent::__construct($name);
    }

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
        )
        ->addOption(
            'no-cache',
            'x',
            InputOption::VALUE_NONE,
            'Do not use cached data'
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
                    return $this->kbize->getBoards($this->input->getOption('project'));
                }
            ],
        ]);
    }

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

    protected function addRequiredOptions(array $required)
    {
        $this->requireOptions = array_merge($this->requireOptions, $required);
    }

    protected function askMissingRequiredOptions(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->requireOptions as $key => $data) {
            $this->askMissingRequiredOption($key, $data, $input, $output);
        }
    }

    protected function askMissingRequiredOption(
        $optionKey,
        array $data,
        InputInterface $input,
        OutputInterface $output
    )
    {
        if (array_key_exists('options', $data)) {
            $options = is_callable($data['options']) ? $data['options']() : $data['options'];

            $currentChoice = $input->getOption($optionKey, null);

            if (!is_null($currentChoice)) {
                if ($this->isValidOptionValue($currentChoice, $options)) {
                    return;
                }

                $output->writeln("<error>`$currentChoice` is invalid value for $optionKey</error>");
                // TODO:! show the error even if the available choices is only one
            }

            $this->input->setOption($optionKey, $this->chooseBetweenMultipleOptions(
                $data['question'],
                $options,
                function ($choice) use ($options, $optionKey, $data) {
                    if (array_key_exists('validation', $data)) {
                        $validation = $data['validation'];
                        return $validation($choice, $options, $optionKey);
                    }

                    return $this->ensureIsValidChoice($choice, $options, $optionKey);
                }
            ));
        } else {

            $currentChoice = $input->getOption($optionKey, null);
            if (!is_null($currentChoice)) {
                return;
            }

            $this->input->setOption($optionKey, $this->getHelperSet()->get('dialog')
                ->ask(
                    $output,
                    $data['question']
                ));
        }
    }

    protected function ensureIsValidChoice($choice, $options, $optionKey)
    {
        if (!$this->isValidOptionValue($choice, $options)) {
            throw new \InvalidArgumentException(
                '`' . $choice . '` is an invalid ' . $optionKey
            );
        }

        return $choice;
    }

    protected function isValidOptionValue($value, $options)
    {
        return in_array($value, array_keys($options));
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->env = $input->getOption('env');
        $this->envConfiguration();
        $this->kbize = new Application($this->env, $output);
        if (!$this->kbize->isAuthenticated()) {
            $this->login();
        }

        if ($input->getOption('no-cache')) {
            $this->kbize->clearCache(false);
        }
    }

    protected function envConfiguration()
    {
        $this->isInteractive = $this->input->isInteractive();
        switch ($this->env) {
        case "test":
            $this->input->setInteractive(true);
            break;
        }
    }

    protected function chooseBetweenMultipleOptions($question, array $options, callable $validation)
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

        $choice = $this->getHelper('dialog')->askAndValidate(
            $this->output,
            $outputQuestion,
            $validation,
            3,
            $defaultChoice
        );

        $this->output->writeln('');

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
                // In test environment, with a non interactive input,
                // askHiddenResponse does not work
                // because it calls system command.
                $method = $this->isInteractive ? 'askHiddenResponse' : 'ask';
                $password = $dialog->$method(
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
}
