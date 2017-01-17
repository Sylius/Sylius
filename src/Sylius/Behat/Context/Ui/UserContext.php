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
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

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
     * @var ShowPageInterface
     */
    private $customerShowPage;

    /**
     * @var HomePageInterface
     */
    private $homePage;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param UserRepositoryInterface $userRepository
     * @param ShowPageInterface $customerShowPage
     * @param HomePageInterface $homePage
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        UserRepositoryInterface $userRepository,
        ShowPageInterface $customerShowPage,
        HomePageInterface $homePage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->userRepository = $userRepository;
        $this->customerShowPage = $customerShowPage;
        $this->homePage = $homePage;
    }

    /**
     * @When I log out
     */
    public function iLogOut()
    {
        $this->homePage->logOut();
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
     * @Then the user account should be deleted
     */
    public function accountShouldBeDeleted()
    {
        $deletedUser = $this->sharedStorage->get('deleted_user');

        $this->customerShowPage->open(['id' => $deletedUser->getCustomer()->getId()]);

        Assert::false($this->customerShowPage->isRegistered());
    }
}
