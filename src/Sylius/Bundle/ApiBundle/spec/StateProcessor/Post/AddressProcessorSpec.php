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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Post;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class AddressProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $persistProcessor,
        UserContextInterface $userContext,
    ): void {
        $this->beConstructedWith($persistProcessor, $userContext);
    }

    function it_throws_an_exception_if_object_is_not_an_address(
        ProcessorInterface $persistProcessor,
        UserContextInterface $userContext,
    ): void {
        $persistProcessor->process(Argument::cetera())->shouldNotBeCalled();
        $userContext->getUser()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [new \stdClass(), new Post()])
        ;
    }

    function it_throws_an_exception_if_operation_is_not_post(
        ProcessorInterface $persistProcessor,
        UserContextInterface $userContext,
    ): void {
        $persistProcessor->process(Argument::cetera())->shouldNotBeCalled();
        $userContext->getUser()->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [new \stdClass(), new Delete()])
        ;
    }

    function it_sets_customer_and_default_address_if_user_is_shop_user(
        ProcessorInterface $persistProcessor,
        UserContextInterface $userContext,
        AddressInterface $address,
        ShopUserInterface $user,
        CustomerInterface $customer,
    ): void {
        $operation = new Post();
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn(null);

        $this->process($address, $operation);

        $persistProcessor->process($address, $operation, [], [])->shouldHaveBeenCalledOnce();
        $address->setCustomer($customer)->shouldHaveBeenCalledOnce();
        $customer->setDefaultAddress($address)->shouldHaveBeenCalledOnce();
    }

    function it_sets_customer_and_default_address_if_user_is_shop_user_and_customer_has_default_address(
        ProcessorInterface $persistProcessor,
        UserContextInterface $userContext,
        AddressInterface $address,
        ShopUserInterface $user,
        CustomerInterface $customer,
        AddressInterface $defaultAddress,
    ): void {
        $operation = new Post();
        $userContext->getUser()->willReturn($user);
        $user->getCustomer()->willReturn($customer);
        $customer->getDefaultAddress()->willReturn($defaultAddress);

        $this->process($address, $operation);

        $persistProcessor->process($address, $operation, [], [])->shouldHaveBeenCalledOnce();
        $address->setCustomer($customer)->shouldHaveBeenCalledOnce();
        $customer->setDefaultAddress($address)->shouldNotHaveBeenCalled();
    }

    function it_does_not_set_customer_and_default_address_if_user_is_not_shop_user(
        ProcessorInterface $persistProcessor,
        UserContextInterface $userContext,
        AddressInterface $address,
    ): void {
        $operation = new Post();
        $userContext->getUser()->willReturn(null);

        $this->process($address, $operation);

        $persistProcessor->process($address, $operation, [], [])->shouldHaveBeenCalledOnce();
        $address->setCustomer(Argument::any())->shouldNotHaveBeenCalled();
    }
}
