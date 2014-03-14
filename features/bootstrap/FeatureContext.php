<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Event\ScenarioEvent,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use KbizeCli\Tests\Integration\Client;
use KbizeCli\Tests\Integration\Cli;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

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
        $fs = new Filesystem();
        $fs->remove('data/test');
        $this->output = null;
    }

    /**
      * @BeforeScenario
      */
    public function createClient(ScenarioEvent $event)
    {
        /* $this->client = new Client(); */
        $this->client = new Cli();
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
        $file = 'data/test/user.yml'; //FIXME:!!!
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * @Given /^I am an authenticated user$/
     */
    public function iAmAnAuthenticatedUser()
    {
        if (!is_dir('data/test')) {
            mkdir ('data/test');
        }

        copy('fixtures/user.yml', 'data/test/user.yml');
    }

    /**
     * @When /^I want to view tasks list$/
     */
    public function iWantToViewTasksList()
    {
        $this->client->command('task:list');
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
        assertContains($text, $output);
    }

    /**
     * @Given /^I should not view in the output "([^"]*)"$/
     */
    public function iShouldNotViewInTheOutput($text)
    {
        $output = $this->execute();
        assertNotContains($text, $output);
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

    private function execute()
    {
        if (!isset($this->output)) {
            $this->client->execute();
            $this->output = $this->client->getDisplay();
        }

        return $this->output;
    }
}
