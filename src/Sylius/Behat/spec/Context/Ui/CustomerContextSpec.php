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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class CustomerContextSpec extends ObjectBehavior
{
    public function let(
        SharedStorageInterface $sharedStorage,
        CustomerShowPage $customerShowPage
    ) {
        $this->beConstructedWith($sharedStorage, $customerShowPage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\CustomerContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_ensures_customer_is_not_deleted_again(
        CustomerShowPage $customerShowPage,
        SharedStorageInterface $sharedStorage,
        CustomerInterface $customer
    ) {
        $sharedStorage->get('customer')->willReturn($customer);

        $customer->getId()->willReturn(1);

        $customerShowPage->open(['id' => 1])->shouldBeCalled();

        $customerShowPage->deleteAccount()->willThrow(new ElementNotFoundException('Element not found.'));
        $this->iShouldNotBeAbleToDeleteCustomerAgain();
    }

    function it_checks_if_customer_still_exists(
        CustomerShowPage $customerShowPage,
        SharedStorageInterface $sharedStorage,
        CustomerInterface $customer,
        UserInterface $user
    ) {
        $sharedStorage->get('deleted_user')->shouldBeCalled()->willReturn($user);
        
        $user->getCustomer()->willReturn($customer);
        
        $customer->getId()->willReturn(1);
        $customerShowPage->open(['id' => 1])->shouldBeCalled();

        $customerShowPage->isRegistered()->willReturn(false);

        $this->customerShouldStillExist();
    }
}
