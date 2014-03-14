<?php
namespace KbizeCli;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use KbizeCli\Sdk\Exception\ForbiddenException;

class Application
{
    private $env;
    private $output;

    public function __construct($env = 'prod', $output)
    {
        $this->env = $env;
        $this->output = $output;

        // create and populate the container
        $this->container = new ContainerBuilder();

        // some useful paths
        $paths = array();
        $paths['root'] = __DIR__ . '/../../';
        $paths['config'] = $paths['root'] . 'app/config/kbize/';
        $this->container->setParameter('paths', $paths);

        // the main config
        $loader = new YamlFileLoader($this->container, new FileLocator($paths['config']));
        $loader->load("config.$env.yml");

        $this->kbize = $this->container->get('kbize');
    }

    public function __call($method, $args)
    {
        return $this->retryOnForbiddenException(function () use ($method, $args) {
            return call_user_func_array([$this->kbize, $method], $args);
        });
    }

    public function getContainer()
    {
        return $this->container;
    }

    private function retryOnForbiddenException(callable $call)
    {
        while (true) {
            try {
                return $call();
            } catch (ForbiddenException $e) {
                $this->renderException($e, $this->output);
                $this->retryOnForbiddenException(function () {
                    return call_user_func([$this, 'authenticate']);
                });
            }
        }
    }

    private function authenticate()
    {
        throw new \RuntimeException('Implement me');
        /* $email = $this->questioner->ask(' */
/* Please insert your Kanbanize email: '); */
        /* $password = $this->questioner->ask( */
        /*     '************************************************************************************* */
/* ATTENTION: your password will NOT be saved, will be used a Kanbanize generated token. */
/* ************************************************************************************* */
/* Please insert your password: ', */
        /*     '', */
        /*     true */
        /* ); */

        /* $this->kbize->login($email, $password); */
    }

    private function renderException($e, $output)
    {
        $strlen = function ($string) {
            if (!function_exists('mb_strlen')) {
                return strlen($string);
            }

            if (false === $encoding = mb_detect_encoding($string)) {
                return strlen($string);
            }

            return mb_strlen($string, $encoding);
        };

        do {
            $title = sprintf('  [%s]  ', get_class($e));
            $len = $strlen($title);
            // HHVM only accepts 32 bits integer in str_split, even when PHP_INT_MAX is a 64 bit integer: https://github.com/facebook/hhvm/issues/1327
            $width = (defined('HHVM_VERSION') ? 1 << 31 : PHP_INT_MAX);
            $formatter = $output->getFormatter();
            $lines = array();
            foreach (preg_split('/\r?\n/', $e->getMessage()) as $line) {
                foreach (str_split($line, $width - 4) as $line) {
                    // pre-format lines to get the right string length
                    $lineLength = $strlen(preg_replace('/\[[^m]*m/', '', $formatter->format($line))) + 4;
                    $lines[] = array($line, $lineLength);

                    $len = max($lineLength, $len);
                }
            }

            $messages = array('', '');
            $messages[] = $emptyLine = $formatter->format(sprintf('<error>%s</error>', str_repeat(' ', $len)));
            $messages[] = $formatter->format(sprintf('<error>%s%s</error>', $title, str_repeat(' ', max(0, $len - $strlen($title)))));
            foreach ($lines as $line) {
                $messages[] = $formatter->format(sprintf('<error>  %s  %s</error>', $line[0], str_repeat(' ', $len - $line[1])));
            }
            $messages[] = $emptyLine;
            $messages[] = '';
            $messages[] = '';

            $output->writeln($messages, OutputInterface::OUTPUT_RAW);

            if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $output->writeln('<comment>Exception trace:</comment>');

                // exception related properties
                $trace = $e->getTrace();
                array_unshift($trace, array(
                    'function' => '',
                    'file'     => $e->getFile() != null ? $e->getFile() : 'n/a',
                    'line'     => $e->getLine() != null ? $e->getLine() : 'n/a',
                    'args'     => array(),
                ));

                for ($i = 0, $count = count($trace); $i < $count; $i++) {
                    $class = isset($trace[$i]['class']) ? $trace[$i]['class'] : '';
                    $type = isset($trace[$i]['type']) ? $trace[$i]['type'] : '';
                    $function = $trace[$i]['function'];
                    $file = isset($trace[$i]['file']) ? $trace[$i]['file'] : 'n/a';
                    $line = isset($trace[$i]['line']) ? $trace[$i]['line'] : 'n/a';

                    $output->writeln(sprintf(' %s%s%s() at <info>%s:%s</info>', $class, $type, $function, $file, $line));
                }

                $output->writeln("");
                $output->writeln("");
            }
        } while ($e = $e->getPrevious());
    }
}
