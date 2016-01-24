<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CartBundle\Provider\CartProvider;
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CartProviderSpec extends ObjectBehavior
{
    function let(
        CartContextInterface $cartContext,
        FactoryInterface $cartFactory,
        RepositoryInterface $cartRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->beConstructedWith($cartContext, $cartFactory, $cartRepository, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartProvider::class);
    }

    function it_implements_Sylius_cart_provider_interface()
    {
        $this->shouldImplement(CartProviderInterface::class);
    }

    function it_looks_for_cart_by_identifier_if_any_in_storage(
        CartContextInterface $cartContext,
        RepositoryInterface $cartRepository,
        EventDispatcherInterface $eventDispatcher,
        CartInterface $cart
    ) {
        $cartContext->getCurrentCartIdentifier()->willReturn(3);
        $cartRepository->find(3)->willReturn($cart);
        $eventDispatcher->dispatch(
            SyliusCartEvents::CART_INITIALIZE,
            Argument::type(GenericEvent::class)
        )->shouldBeCalled();

        $cartContext->setCurrentCartIdentifier($cart)->shouldNotBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_creates_new_cart_if_there_is_no_identifier_in_storage(
        CartContextInterface $cartContext,
        FactoryInterface $cartFactory,
        RepositoryInterface $cartRepository,
        EventDispatcherInterface $eventDispatcher,
        CartInterface $cart
    ) {
        $cartContext->getCurrentCartIdentifier()->willReturn(null);
        $cartFactory->createNew()->willReturn($cart);
        $cartRepository->find()->shouldNotBeCalled();

        $eventDispatcher->dispatch(
            SyliusCartEvents::CART_INITIALIZE,
            Argument::type(GenericEvent::class)
        )->shouldBeCalled();

        $cartContext->setCurrentCartIdentifier($cart)->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_creates_new_cart_if_identifier_is_wrong(
        CartContextInterface $cartContext,
        FactoryInterface $cartFactory,
        RepositoryInterface $cartRepository,
        EventDispatcherInterface $eventDispatcher,
        CartInterface $cart
    ) {
        $cartContext->getCurrentCartIdentifier()->willReturn(7);
        $cartRepository->find(7)->shouldBeCalled()->willReturn(null);
        $cartFactory->createNew()->willReturn($cart);

        $cartContext->setCurrentCartIdentifier($cart)->shouldBeCalled();

        $eventDispatcher->dispatch(
            SyliusCartEvents::CART_INITIALIZE,
            Argument::type(GenericEvent::class)
        )->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_resets_current_cart_identifier_in_storage_when_abandoning_cart(
        CartContextInterface $cartContext,
        RepositoryInterface $cartRepository,
        EventDispatcherInterface $eventDispatcher,
        CartInterface $cart
    ) {
        $cartContext->getCurrentCartIdentifier()->willReturn(123);
        $cartRepository->find(123)->willReturn($cart);

        $cartContext->resetCurrentCartIdentifier()->shouldBeCalled();

        $eventDispatcher->dispatch(
            SyliusCartEvents::CART_ABANDON,
            Argument::type(GenericEvent::class)
        )->shouldBeCalled();

        $this->abandonCart();
    }

    function it_sets_current_cart_identifier_when_setting_cart(
        CartContextInterface $cartContext,
        CartInterface $cart
    ) {
        $cartContext->setCurrentCartIdentifier($cart)->shouldBeCalled();

        $this->setCart($cart);
    }

    function it_initializes_cart_while_validating_existence_and_if_there_is_no_identifier_in_storage(
        CartContextInterface $cartContext
    ) {
        $cartContext->getCurrentCartIdentifier()->willReturn(null);

        $this->hasCart()->shouldReturn(false);
    }

    function it_initializes_cart_while_validating_existence_and_if_there_is_identifier_in_storage(
        CartContextInterface $cartContext
    ) {
        $cartContext->getCurrentCartIdentifier()->willReturn(666);

        $this->hasCart()->shouldReturn(true);
    }
}
