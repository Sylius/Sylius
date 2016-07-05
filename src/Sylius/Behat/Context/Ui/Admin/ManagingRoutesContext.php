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
}
