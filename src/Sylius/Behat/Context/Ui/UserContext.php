<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Customer\CustomerShowPageInterface;
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\User\LoginPageInterface;
use Sylius\Behat\Page\User\RegisterPageInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class UserContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var CustomerShowPageInterface
     */
    private $customerShowPage;

    /**
     * @var LoginPageInterface
     */
    private $loginPage;

    /**
     * @var RegisterPageInterface
     */
    private $registerPage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param UserRepositoryInterface $userRepository
     * @param CustomerShowPageInterface $customerShowPage
     * @param LoginPageInterface $loginPage
     * @param RegisterPageInterface $registerPage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        UserRepositoryInterface $userRepository,
        CustomerShowPageInterface $customerShowPage,
        LoginPageInterface $loginPage,
        RegisterPageInterface $registerPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->userRepository = $userRepository;
        $this->customerShowPage = $customerShowPage;
        $this->loginPage = $loginPage;
        $this->registerPage = $registerPage;
    }

    /**
     * @Given /^I log in as "([^"]*)" with "([^"]*)" password$/
     */
    public function iLogInAs($login, $password)
    {
        $this->loginPage->open();
        $this->loginPage->logIn($login, $password);
    }

    /**
     * @When I try to register again with email :email
     */
    public function iTryToRegister($email)
    {
        $this->registerPage->open();
        $this->registerPage->register($email);
    }

    /**
     * @Then I should be successfully registered
     */
    public function iShouldBeRegistered()
    {
        expect($this->registerPage->wasRegistrationSuccessful())->toBe(true);
    }

    /**
     * @When I delete the account of :email user
     */
    public function iDeleteAccount($email)
    {
        $user = $this->userRepository->findOneByEmail($email);

        $this->sharedStorage->set('deleted_user', $user);

        $this->customerShowPage->open(['id' => $user->getCustomer()->getId()]);
        $this->customerShowPage->deleteAccount();
    }

    /**
     * @When I try to delete my own account
     */
    public function iTryDeletingMyOwnAccount()
    {
        $admin = $this->sharedStorage->get('admin');

        $this->customerShowPage->open(['id' => $admin->getId()]);
    }

    /**
     * @Then I should not be able to do it
     */
    public function iShouldNotBeAbleToDeleteMyOwnAccount()
    {
        expect($this->customerShowPage)->toThrow(new ElementNotFoundException('Element not found.'))->during('deleteAccount');
    }

    /**
     * @Then the user account should be deleted
     */
    public function accountShouldBeDeleted()
    {
        $deletedUser = $this->sharedStorage->get('deleted_user');

        $this->customerShowPage->open(['id' => $deletedUser->getCustomer()->getId()]);

        expect($this->customerShowPage->isRegistered())->toBe(false);
    }
}
