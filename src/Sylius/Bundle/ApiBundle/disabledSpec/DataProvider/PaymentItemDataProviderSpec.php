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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class PaymentItemDataProviderSpec extends ObjectBehavior
{
    function let(PaymentRepositoryInterface $paymentRepository, UserContextInterface $userContext): void
    {
        $this->beConstructedWith($paymentRepository, $userContext);
    }

    function it_supports_only_payment(): void
    {
        $this->supports(PaymentInterface::class, 'get')->shouldReturn(true);
        $this->supports(ResourceInterface::class, 'get')->shouldReturn(false);
    }

    function it_provides_payment_for_shop_user(
        PaymentRepositoryInterface $paymentRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        PaymentInterface $payment,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $paymentRepository
            ->findOneByCustomer('123', $customer->getWrappedObject())
            ->willReturn($payment)
        ;

        $this->getItem(PaymentInterface::class, '123')->shouldReturn($payment);
    }

    function it_provides_payment_for_admin_user(
        PaymentRepositoryInterface $paymentRepository,
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        PaymentInterface $payment,
    ) {
        $userContext->getUser()->willReturn($adminUser);

        $adminUser->getRoles()->willReturn(['ROLE_API_ACCESS']);

        $paymentRepository->find('123')->willReturn($payment);

        $this->getItem(PaymentInterface::class, '123')->shouldReturn($payment);
    }

    function it_returns_null_if_shop_user_has_no_customer(
        PaymentRepositoryInterface $paymentRepository,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn(null);
        $shopUser->getRoles()->willReturn(['ROLE_USER']);

        $paymentRepository->findOneByCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(PaymentInterface::class, '123')->shouldReturn(null);
    }

    function it_returns_null_if_shop_user_does_have_proper_roles(
        PaymentRepositoryInterface $paymentRepository,
        UserContextInterface $userContext,
        CustomerInterface $customer,
        ShopUserInterface $shopUser,
    ) {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);
        $shopUser->getRoles()->willReturn(['']);

        $paymentRepository->findOneByCustomer('123', new Customer())->shouldNotBeCalled();

        $this->getItem(PaymentInterface::class, '123')->shouldReturn(null);
    }
}
