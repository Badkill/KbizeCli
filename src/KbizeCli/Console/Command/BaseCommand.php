<?php
namespace KbizeCli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use KbizeCli\Questioner;

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
        foreach ($this->requireOptions as $option => $label) {
            if (is_null($input->getOption($option, null))) {
                $input->setOption($option, $this->ask($label));
            }
        }
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->askMissingRequiredOptions($input, $output);
    }
}
