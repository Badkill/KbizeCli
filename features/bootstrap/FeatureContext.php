<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Beha\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Event\ScenarioEvent,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use KbizeCli\Tests\Integration\Client;
use KbizeCli\Tests\Integration\Cli;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use KbizeCli\Application;

//
// Require 3rd-party libraries here:
//
require_once 'PHPUnit/Autoload.php';
require_once 'PHPUnit/Framework/Assert/Functions.php';

require 'bootstrap.php';

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
        $application = new Application('test', null);
        $this->pathData = $application->getContainer()->getParameter('path.data');
        error_log($this->pathData);
        $fs = new Filesystem();
        $fs->remove($this->pathData);
        $this->output = null;
    }

    /**
     * @BeforeScenario
     */
    public function createClient(ScenarioEvent $event)
    {
        /* $this->client = new Client(); */
        $this->client = new Cli($this);
    }

    /**
     * @AfterScenario
     */
    public function cleanTmp()
    {
        $fs = new Filesystem();
        $fs->remove(sys_get_temp_dir() . '/kbizeCliTmpData');
    }

    /**
     * @AfterScenario
     */
    public function destroyClient(ScenarioEvent $event)
    {
        $this->client->__destruct();
    }

    /**
     * @Given /^I am an unauthenticated user$/
     */
    public function iAmAnUnauthenticatedUser()
    {
        $file = $this->pathData . '/user.yml'; //FIXME:!!!
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * @Given /^I am an authenticated user$/
     */
    public function iAmAnAuthenticatedUser()
    {
        if (!is_dir($this->pathData)) {
            mkdir ($this->pathData);
        }

        copy('fixtures/user.yml', $this->pathData . '/user.yml');
    }

    /**
     * @When /^I want to view tasks list$/
     */
    public function iWantToViewTasksList()
    {
        $this->client->command('task:list');
        $this->output = null;
    }

    /**
     * @When /^I want to create a new tasks$/
     */
    public function iWantToCreateANewTasks()
    {
        $this->client->command('task:create');
        $this->output = null;
    }

    /**
     * @Then /^I should insert [^"]* "([^"]*)"$/
     * @Then /^I insert [^"]* "([^"]*)"$/
     */
    public function iShouldInsertBoardid($input)
    {
        $this->client->input($input);
    }

    /**
     * @Given /^I should view in the output "([^"]*)"$/
     */
    public function iShouldViewInTheOutput($text)
    {
        $output = $this->execute();
        assertRegExp('/' . $text . '/', $output);
    }

    /**
     * @Given /^I should not view in the output "([^"]*)"$/
     */
    public function iShouldNotViewInTheOutput($text)
    {
        $output = $this->execute();
        assertNotRegExp('/' . $text . '/', $output);
    }

    /**
     * @Given /^The client has no more input$/
     */
    public function theClientHasNoMoreInput()
    {
        throw new PendingException();
        $this->client->ensureIsEmpty();
    }

    /**
     * @Given /^I use the option \"([^ ]*) ([^\"]*)\"$/
     * @Given /^I use the option "([^" ]*)"$/
     */
    public function iUseTheOption($option, $value = true)
    {
        $this->client->addOption($option, $value);
    }

    /**
     * @Given /^I\'m waiting task creation$/
     */
    public function iMWaitCreationOfTask()
    {
        sleep(2); //FIXME:!!!!
    }

    private function execute()
    {
        if (!isset($this->output)) {
            $this->client->execute();
            $this->output = $this->client->getDisplay();
        }

        return $this->output;
    }
}
