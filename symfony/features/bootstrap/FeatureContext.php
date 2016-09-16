<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context, SnippetAcceptingContext
{
    private $items;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given I have a list of items
     */
    public function iHaveAListOfItems()
    {
        $this->items = ['foo', 'bar'];
    }

    /**
     * @Then I should have two :arg1 items in the list
     */
    public function iShouldHaveTwoItemsInTheList($arg1)
    {
        PHPUnit_Framework_Assert::assertCount($arg1*1, $this->items);
    }
}
