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
use Sylius\Behat\Page\Admin\Administrator\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\Administrator\CreatePageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingAdministratorsContext implements Context
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
     * @Given I want to create a new administrator
     */
    public function iWantToCreateANewAdministrator()
    {
        $this->createPage->open();
    }

    /**
     * @Given /^I want to edit (this administrator)$/
     */
    public function iWantToEditThisAdministrator(AdminUserInterface $adminUser)
    {
        $this->updatePage->open(['id' => $adminUser->getId()]);
    }

    /**
     * @When I want to see all administrators in store
     */
    public function iWantToSeeAllAdministratorsInStore()
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its name as :username
     */
    public function iSpecifyItsNameAs($username)
    {
        $this->createPage->specifyUsername($username);
    }

    /**
     * @When I change its name as :username
     */
    public function iChangeItsNameAs($username)
    {
        $this->updatePage->changeUsername($username);
    }

    /**
     * @When I specify its email as :email
     */
    public function iSpecifyItsEmailAs($email)
    {
        $this->createPage->specifyEmail($email);
    }

    /**
     * @When I change its email as :email
     */
    public function iChangeItsEmailAs($email)
    {
        $this->updatePage->changeEmail($email);
    }

    /**
     * @When I specify its password as :password
     */
    public function iSpecifyItsPasswordAs($password)
    {
        $this->createPage->specifyPassword($password);
    }

    /**
     * @When I change its password as :password
     */
    public function iChangeItsPasswordAs($password)
    {
        $this->updatePage->changePassword($password);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt()
    {
        $this->createPage->enable();
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
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then the administrator :email should appear in the store
     * @Then I should see the administrator :email in the list
     */
    public function theAdministratorShouldAppearInTheStore($email)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['email' => $email]),
            sprintf('Administrator %s does not exist', $email)
        );
    }

    /**
     * @Then this administrator with name :username should appear in the store
     */
    public function thisAdministratorWithNameShouldAppearInTheStore($username)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['username' => $username]),
            sprintf('Administrator with %s username does not exist', $username)
        );
    }

    /**
     * @Then /^I should see (\d+) administrators in the list$/
     */
    public function iShouldSeeAdministratorsInTheList($number)
    {
        Assert::same(
            $this->indexPage->countItems(),
            $number,
            sprintf('There should be %s administrators, but got %s', $number, $this->indexPage->countItems())
        );
    }
}
