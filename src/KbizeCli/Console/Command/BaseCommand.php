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
abstract class BaseCommand extends Command implements Questioner
{
    protected $requiredOptions;

    public function ask($question, $default = '', $hiddenResponse = false)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $method = $hiddenResponse ? 'askHiddenResponse' : 'ask';
        return $dialog->$method(
            $this->output,
            $question,
            $default
        );
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

    protected function askMissingRequiredOptions(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->requireOptions as $key => $data) {
            $output->writeln('');

            if (!is_null($input->getOption($key, null))) {
                continue;
            }

            $options = is_callable($data['options']) ? $data['options']() : $data['options'];

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
        $this->input = $input;
        $this->output = $output;
        $this->kbize = new Application($input->getOption('env'), $this, $output);
    }

    protected function askForMultipleOptions($question, array $options, callable $validation) //FIXME:! RENAME IT
    {
        $defaultChoice = array_keys($options)[0];
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
        $this->kbize->authenticate();
        $this->askMissingRequiredOptions($input, $output);
    }

    /* protected function interact(InputInterface $input, OutputInterface $output) */
    /* { */
    /*     echo "pippo"; */
    /*     exit; */

    /*     $defaultType = 1; */
    /*     $question = array( */
    /*         "<comment>1</comment>: Messages\n", */
    /*         "<comment>2</comment>: Jobs\n", */
    /*         "<question>Choose a type:</question> [<comment>$defaultType</comment>] ", */
    /*     ); */

    /*     $type = $this->getHelper('dialog')->askAndValidate($output, $question, function($typeInput) { */
    /*         if (!in_array($typeInput, array(1, 2))) { */
    /*             throw new \InvalidArgumentException('Invalid type'); */
    /*         } */

    /*         return $typeInput; */
    /*     }, 10, $defaultType); */

    /*     $input->setArgument('type', $type); */
    /* } */
}
