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
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Page\Admin\Customer\ShowPageInterface;
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\Shop\User\LoginPageInterface;
use Sylius\Behat\Page\Shop\User\RegisterPageInterface;
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
        ShowPageInterface $customerShowPage,
        LoginPageInterface $loginPage,
        RegisterPageInterface $registerPage
    ) {
        $this->beConstructedWith($sharedStorage, $userRepository, $customerShowPage, $loginPage, $registerPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\UserContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_logs_in_user_with_given_credentials(LoginPageInterface $loginPage)
    {
        $loginPage->open()->shouldBeCalled();
        $loginPage->logIn('john.doe@example.com', 'password123')->shouldBeCalled();

        $this->iLogInAs('john.doe@example.com', 'password123');
    }

    function it_deletes_account(
        UserRepositoryInterface $userRepository,
        ShowPageInterface $customerShowPage,
        SharedStorageInterface $sharedStorage,
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
        SharedStorageInterface $sharedStorage,
        UserInterface $admin,
        ShowPageInterface $customerShowPage
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
        ShowPageInterface $customerShowPage
    ) {
        $sharedStorage->get('deleted_user')->willReturn($user);

        $user->getCustomer()->willReturn($customer);
        $customer->getId()->willReturn(1);

        $customerShowPage->open(['id' => 1])->shouldBeCalled();

        $customerShowPage->isRegistered()->willReturn(false);

        $this->accountShouldBeDeleted();
    }

    function it_tries_to_register_new_account(RegisterPageInterface $registerPage)
    {
        $registerPage->open()->shouldBeCalled();
        $registerPage->register('ted@example.com')->shouldBeCalled();

        $this->iTryToRegister('ted@example.com');
    }

    function it_checks_if_account_is_registered_successfully(RegisterPageInterface $registerPage)
    {
        $registerPage->wasRegistrationSuccessful()->willReturn(true);

        $this->iShouldBeRegistered();
    }

    function it_checks_if_account_registration_was_not_successful(RegisterPageInterface $registerPage)
    {
        $registerPage->wasRegistrationSuccessful()->willReturn(false);

        $this->shouldThrow(FailureException::class)->during('iShouldBeRegistered');
    }
}
