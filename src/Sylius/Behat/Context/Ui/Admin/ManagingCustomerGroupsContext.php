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
use Sylius\Behat\Page\Admin\CustomerGroup\CreatePageInterface;
use Sylius\Behat\Page\Admin\CustomerGroup\UpdatePageInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ManagingCustomerGroupsContext implements Context
{
    /**
     * @var CreatePageInterface
     */
    private $createPage;
    
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
    }

    /**
     * @Given I want to create a new customer group
     */
    public function iWantToCreateANewCustomerGroup()
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its name as :name
     * @When I remove its name
     */
    public function iSpecifyItsNameAs($name = null)
    {
        $this->createPage->nameIt($name);
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
     * @Then the customer group :customerGroup should appear in the store
     */
    public function theCustomerGroupShouldAppearInTheStore(CustomerGroupInterface $customerGroup)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $customerGroup->getName()]),
            sprintf('Customer group with name %s should exist but it does not.', $customerGroup->getName())
        );
    }

    /**
     * @When /^I want to edit (this customer group)$/
     * @When I want to edit the customer group :customerGroup
     */
    public function iWantToEditThisCustomerGroup(CustomerGroupInterface $customerGroup)
    {
        $this->updatePage->open(['id' => $customerGroup->getId()]);
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
     * @Then this customer group with name :name should appear in the store
     * @Then I should see the customer group :name in the list
     *
     */
    public function thisCustomerGroupWithNameShouldAppearInTheStore($name)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $name]),
            sprintf('The customer group with a name %s should exist, but it does not.', $name)
        );
    }

    /**
     * @When I want to browse customer groups of the store
     */
    public function iWantToBrowseCustomerGroupsOfTheStore()
    {
        $this->indexPage->open();
    }

    /**
     * @Then /^I should see (\d+) customer groups in the list$/
     */
    public function iShouldSeeCustomerGroupsInTheList($amountOfCustomerGroups)
    {
        Assert::same(
            (int) $amountOfCustomerGroups,
            $this->indexPage->countItems(),
            sprintf('Amount of customer groups should be equal %s, but is not.', $amountOfCustomerGroups)
        );
    }

    /**
     * @Then /^(this customer group) should still be named "([^"]+)"$/
     */
    public function thisChannelNameShouldBe(CustomerGroupInterface $customerGroup, $customerGroupName)
    {
        $this->iWantToBrowseCustomerGroupsOfTheStore();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $customerGroup->getName()]
            ),
            sprintf('Customer group name %s has not been assigned properly.', $customerGroupName)
        );
    }

    /**
     * @Then I should be notified that name is required
     */
    public function iShouldBeNotifiedThatNameIsRequired()
    {
        Assert::same(
            $this->updatePage->getValidationMessage('name'),
            'Please enter a customer group name.'
        );
    }
}
