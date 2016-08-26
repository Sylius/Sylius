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
use Sylius\Behat\Page\Admin\StaticContent\CreatePageInterface;
use Sylius\Behat\Page\Admin\StaticContent\UpdatePageInterface;
use Sylius\Bundle\ContentBundle\Document\StaticContent;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ManagingStaticContentsContext implements Context
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
     * @Given I want to create a new static content
     * @Given I want to add a new static content
     */
    public function iWantToCreateNewStaticContent()
    {
        $this->createPage->open();
    }

    /**
     * @When I want to browse static contents of the store
     */
    public function iWantToBrowseStaticContentsOfTheStore()
    {
        $this->indexPage->open();
    }

    /**
     * @When I set its title to :title
     */
    public function iSetItsTitleTo($title)
    {
        $this->createPage->setTitle($title);
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
     * @Then /^I should be notified that (body|name|title) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        Assert::same(
            $this->createPage->getValidationMessage($element),
            'This value should not be blank.'
        );
    }

    /**
     * @Then the static content :title should appear in the store
     * @Then I should see the static content :title in the list
     */
    public function theStaticContentShouldAppearInTheStore($title)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['title' => $title]),
            sprintf('Could not find static content with title "%s"!', $title)
        );
    }

    /**
     * @Then I should see :amount static contents in the list
     */
    public function iShouldSeeThatManyStaticContentsInTheList($amount)
    {
        Assert::same(
            (int) $amount,
            $this->indexPage->countItems(),
            'Amount of static contents should be equal %s, but was %2$s.'
        );
    }

    /**
     * @Then the static content :title should not be added
     */
    public function theStaticContentShouldNotBeAdded($title)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['title' => $title]),
            sprintf('Static content with title %s was created, but it should not.', $title)
        );
    }

    /**
     * @Given /^I want to edit (this static content)$/
     */
    public function iWantToEditThisStaticContent(StaticContent $staticContent)
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
     * @When I delete static content :title
     */
    public function iDeleteStaticContent($title)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['title' => $title]);
    }

    /**
     * @Then /^(this static content) should have body "([^"]+)"$/
     */
    public function thisStaticContentShouldHaveBody(StaticContent $staticContent, $body)
    {
        $this->updatePage->open(['id' => $staticContent->getId()]);

        Assert::same($this->updatePage->getBody(), $body);
    }

    /**
     * @Then the static content :title should no longer exist in the store
     */
    public function theStaticContentShouldNoLongerExistInTheStore($title)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['title' => $title]),
            sprintf('Static content with title %s exists, but should not.', $title)
        );
    }
}
