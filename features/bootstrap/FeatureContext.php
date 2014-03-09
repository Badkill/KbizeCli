<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use KbizeCli\Tests\Integration\Client;
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
    }

    /**
     * @Given /^I am unauthenticated user$/
     */
    public function iAmUnauthenticatedUser()
    {
        $file = 'data/test/user.yml'; //FIXME:!!!
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * @When /^I want to view tasks list$/
     */
    public function iWantToViewTasksList()
    {
        $this->client = new Client();
    }

    /**
     * @Then /^I should insert [^"]* "([^"]*)"$/
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
        if (!isset($this->output)) {
            $this->client->command()->execute();
            $this->output = $this->client->getDisplay();
        }

        assertContains($text, $this->output);
    }

    /**
     * @Given /^The client has no more input$/
     */
    public function theClientHasNoMoreInput()
    {
        $this->client->ensureIsEmpty();
    }
}
