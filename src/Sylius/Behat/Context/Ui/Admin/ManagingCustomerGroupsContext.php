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
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
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

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $customerGroup->getName()]));
    }

    /**
     * @When /^I want to edit (this customer group)$/
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

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @When I want to browse customer groups
     */
    public function iWantToBrowseCustomerGroups()
    {
        $this->indexPage->open();
    }

    /**
     * @Then /^I should see (\d+) customer groups in the list$/
     */
    public function iShouldSeeCustomerGroupsInTheList($amountOfCustomerGroups)
    {
        $this->indexPage->open();

        Assert::same($this->indexPage->countItems(), (int) $amountOfCustomerGroups);
    }

    /**
     * @Then /^(this customer group) should still be named "([^"]+)"$/
     */
    public function thisCustomerGroupShouldStillBeNamed(CustomerGroupInterface $customerGroup, $customerGroupName)
    {
        $this->iWantToBrowseCustomerGroups();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $customerGroupName]));
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

    /**
     * @Then I should be notified that customer group with this code already exists
     */
    public function iShouldBeNotifiedThatCustomerGroupWithThisCodeAlreadyExists()
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'Customer group code has to be unique.');
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @When I delete the :customerGroup customer group
     */
    public function iDeleteTheCustomerGroup(CustomerGroupInterface $customerGroup)
    {
        $this->iWantToBrowseCustomerGroups();

        $this->indexPage->deleteResourceOnPage(['name' => $customerGroup->getName()]);
    }

    /**
     * @Then /^(this customer group) should no longer exist in the registry$/
     */
    public function thisCustomerGroupShouldNoLongerExistInTheRegistry(CustomerGroupInterface $customerGroup)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['name' => $customerGroup->getName()]),
            sprintf(
                'Customer group %s should no longer exist in the registry',
                $customerGroup->getName()
            )
        );
    }
}
