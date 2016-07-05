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
use Sylius\Behat\Page\Admin\Route\CreatePageInterface;
use Sylius\Behat\Page\Admin\Route\UpdatePageInterface;
use Sylius\Bundle\ContentBundle\Document\Route;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ManagingRoutesContext implements Context
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
     * @Given I want to create a new route
     * @Given I want to add a new route
     */
    public function iWantToCreateNewRoute()
    {
        $this->createPage->open();
    }

    /**
     * @When I want to browse routes of the store
     */
    public function iWantToBrowseRoutesOfTheStore()
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
     * @When I choose :title as its content
     */
    public function iChooseContent($title)
    {
        $this->createPage->chooseContent($title);
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
     * @Then the route :name should appear in the store
     * @Then I should see the route :name in the list
     */
    public function theRouteShouldAppearInTheStore($name)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $name]),
            sprintf('Could not find route with name "%s"!', $name)
        );
    }

    /**
     * @Then I should see :amount routes in the list
     */
    public function iShouldSeeThatManyRoutesInTheList($amount)
    {
        Assert::same(
            (int) $amount,
            $this->indexPage->countItems(),
            'Amount of routes should be equal %s, but was %2$s.'
        );
    }

    /**
     * @When I delete route :name
     */
    public function iDeleteRoute($name)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $name]);
    }

    /**
     * @Given the route :name should no longer exist in the store
     */
    public function theRouteShouldNoLongerExistInTheStore($name)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['name' => $name]),
            sprintf('Route with name "%s" exists, but should not.', $name)
        );
    }

    /**
     * @Given /^I want to edit (this route)$/
     */
    public function iWantToEditThisRoute(Route $route)
    {
        $this->updatePage->open(['id' => $route->getId()]);
    }

    /**
     * @When I choose :title as its new content
     */
    public function iChooseNewContent($title)
    {
        $this->updatePage->chooseNewContent($title);
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
     * @Then /^(this route) should have assigned "([^"]+)" content$/
     */
    public function thisRouteShouldHaveAssignedContent(Route $route, $contentTitle)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $route->getName(), 'content' => $contentTitle]),
            sprintf('Cannot find route with name "%s" and content "%s" assigned.', $route->getName(), $contentTitle)
        );
    }

    /**
     * @Then I should be notified that name is required
     */
    public function iShouldBeNotifiedThatElementIsRequired()
    {
        Assert::same(
            $this->createPage->getValidationMessage('name'),
            'This value should not be blank.'
        );
    }

    /**
     * @Then the route with content :title should not be added
     */
    public function theRouteWithContentShouldNotBeAdded($title)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['content' => $title]),
            sprintf('Found route with content "%s" assigned, but expected not to.', $title)
        );
    }
}
