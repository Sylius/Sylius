<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\Customer\CustomerShowPage;
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\User\LoginPage;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserContextSpec extends ObjectBehavior
{
    public function let(
        SharedStorageInterface $sharedStorage,
        UserRepositoryInterface $userRepository,
        CustomerShowPage $customerShowPage,
        LoginPage $loginPage
    ) {
        $this->beConstructedWith($sharedStorage, $userRepository, $customerShowPage, $loginPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\UserContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_logs_in_user_with_given_credentials($loginPage)
    {
        $loginPage->open()->shouldBeCalled();
        $loginPage->logIn('john.doe@example.com', 'password123')->shouldBeCalled();

        $this->iLogInAs('john.doe@example.com', 'password123');
    }

    function it_deletes_account(
        $userRepository,
        $customerShowPage,
        $sharedStorage,
        UserInterface $user,
        CustomerInterface $customer
    ) {
        $userRepository->findOneByEmail('theodore@test.com')->willReturn($user);
        $sharedStorage->set('deleted_user', $user)->shouldBeCalled();

        $user ->getCustomer()->willReturn($customer);

        $customer->getId()->willReturn(1);
        $customerShowPage->open(['id' => 1])->shouldBeCalled();

        $customerShowPage->deleteAccount()->shouldBeCalled();

        $this->iDeleteAccount('theodore@test.com');
    }

    function it_does_not_allow_deleting_my_own_account(
        SharedStorageInterface $sharedStorage, UserInterface $admin, CustomerShowPage $customerShowPage
    ) {
        $sharedStorage->get('admin')->willReturn($admin);

        $admin->getId()->willReturn(1);
        $customerShowPage->open(['id' => 1])->shouldBeCalled();
        $this->iTryDeletingMyOwnAccount();

        $customerShowPage->deleteAccount()->willThrow(new ElementNotFoundException('Element not found.'));
        $this->iShouldNotBeAbleToDeleteMyOwnAccount();
    }

    function it_checks_if_account_was_deleted(
        SharedStorageInterface $sharedStorage,
        UserInterface $user,
        CustomerInterface $customer,
        CustomerShowPage $customerShowPage
    ) {
        $sharedStorage->get('deleted_user')->willReturn($user);

        $user->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);

        $customerShowPage->open(['id' => 1])->shouldBeCalled();

        $customerShowPage->isRegistered()->willReturn(false);

        $this->accountShouldBeDeleted();
    }
}
