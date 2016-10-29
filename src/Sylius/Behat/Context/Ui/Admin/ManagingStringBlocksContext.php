<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\StringBlock\CreatePageInterface;
use Sylius\Behat\Page\Admin\StringBlock\UpdatePageInterface;
use Sylius\Bundle\ContentBundle\Document\StringBlock;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ManagingStringBlocksContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
    }

    /**
     * @Given I want to create a new string block
     * @Given I want to add a new string block
     */
    public function iWantToCreateNewStringBlock()
    {
        $this->createPage->open();
    }

    /**
     * @Given I browse string blocks of the store
     */
    public function iWantToBrowseStringBlocksOfTheStore()
    {
        $this->indexPage->open();
    }

    /**
     * @When I set its name to :name
     */
    public function iSetItsNameTo($name)
    {
        $this->createPage->setName($name);
    }

    /**
     * @When I set its body to :body
     */
    public function iSetItsBodyTo($body)
    {
        $this->createPage->setBody($body);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then /^I should be notified that (body|name|name) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        Assert::same(
            $this->createPage->getValidationMessage($element),
            'This value should not be blank.'
        );
    }

    /**
     * @Then the string block :name should appear in the store
     * @Then I should see the string block :name in the list
     */
    public function theStringBlockShouldAppearInTheStore($name)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $name]),
            sprintf('Could not find string block with name "%s"!', $name)
        );
    }

    /**
     * @Then I should see :amount string blocks in the list
     */
    public function iShouldSeeThatManyStringBlocksInTheList($amount)
    {
        Assert::same(
            (int) $amount,
            $this->indexPage->countItems(),
            'Amount of string blocks should be equal %s, but was %2$s.'
        );
    }

    /**
     * @Then the string block :name should not be added
     */
    public function theStringBlockShouldNotBeAdded($name)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['name' => $name]),
            sprintf('Static content with name %s was created, but it should not.', $name)
        );
    }

    /**
     * @Given /^I want to edit (this string block)$/
     */
    public function iWantToEditThisStringBlock(StringBlock $staticContent)
    {
        $this->updatePage->open(['id' => $staticContent->getId()]);
    }

    /**
     * @When I change its body to :body
     */
    public function iChangeItsBodyTo($body)
    {
        $this->updatePage->changeBodyTo($body);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete string block :name
     */
    public function iDeleteStringBlock($name)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $name]);
    }

    /**
     * @Then /^(this string block) should have body "([^"]+)"$/
     */
    public function thisStringBlockShouldHaveBody(StringBlock $staticContent, $body)
    {
        $this->updatePage->open(['id' => $staticContent->getId()]);

        Assert::same($this->updatePage->getBody(), $body);
    }

    /**
     * @Then the string block :name should no longer exist in the store
     */
    public function theStringBlockShouldNoLongerExistInTheStore($name)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['name' => $name]),
            sprintf('Static content with name %s exists, but should not.', $name)
        );
    }
}
