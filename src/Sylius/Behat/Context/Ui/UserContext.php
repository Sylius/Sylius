<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Behat\Page\Shop\HomePageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

final class UserContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private UserRepositoryInterface $userRepository,
        private ShowPageInterface $customerShowPage,
        private HomePageInterface $homePage,
    ) {
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
        /** @var ShopUserInterface $user */
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
