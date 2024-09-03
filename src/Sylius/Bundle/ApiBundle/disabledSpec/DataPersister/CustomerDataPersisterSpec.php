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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;

final class CustomerDataPersisterSpec extends ObjectBehavior
{
    function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        PasswordUpdaterInterface $passwordUpdater,
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $passwordUpdater);
    }

    function it_supports_only_customer_entity(CustomerInterface $customer, ProductInterface $product): void
    {
        $this->supports($customer)->shouldReturn(true);
        $this->supports($product)->shouldReturn(false);
    }

    function it_does_not_update_password_when_user_is_null(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        PasswordUpdaterInterface $passwordUpdater,
        CustomerInterface $customer,
    ): void {
        $customer->getUser()->willReturn(null);
        $passwordUpdater->updatePassword(Argument::any())->shouldNotBeCalled();

        $decoratedDataPersister->persist($customer, [])->shouldBeCalled();

        $this->persist($customer, []);
    }

    function it_does_not_update_password_when_plain_password_is_null(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        PasswordUpdaterInterface $passwordUpdater,
        CustomerInterface $customer,
        ShopUserInterface $user,
    ): void {
        $user->getPlainPassword()->willReturn(null);
        $customer->getUser()->willReturn($user);
        $passwordUpdater->updatePassword($user)->shouldNotBeCalled();

        $decoratedDataPersister->persist($customer, [])->shouldBeCalled();

        $this->persist($customer, []);
    }

    function it_updates_password_when_plain_password_is_set(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        PasswordUpdaterInterface $passwordUpdater,
        CustomerInterface $customer,
        ShopUserInterface $user,
    ): void {
        $user->getPlainPassword()->willReturn('password');
        $customer->getUser()->willReturn($user);
        $passwordUpdater->updatePassword($user)->shouldBeCalled();

        $decoratedDataPersister->persist($customer, [])->shouldBeCalled();

        $this->persist($customer, []);
    }

    function it_uses_decorated_data_persister_to_remove_customer(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        CustomerInterface $customer,
    ): void {
        $decoratedDataPersister->remove($customer, [])->shouldBeCalled();

        $this->remove($customer, []);
    }
}
