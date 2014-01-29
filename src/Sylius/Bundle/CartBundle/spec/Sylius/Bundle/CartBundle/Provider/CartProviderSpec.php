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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Storage\CartStorageInterface;
use Sylius\Bundle\CartBundle\SyliusCartEvents;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartProviderSpec extends ObjectBehavior
{
    function let(CartStorageInterface $storage, ObjectManager $manager, RepositoryInterface $repository, EventDispatcherInterface $eventDispatcher)
    {
        $this->beConstructedWith($storage, $manager, $repository, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Provider\CartProvider');
    }

    function it_implements_Sylius_cart_provider_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Provider\CartProviderInterface');
    }

    function it_looks_for_cart_by_identifier_if_any_in_storage($storage, $repository, $eventDispatcher, CartInterface $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(3);
        $repository->find(3)->shouldBeCalled()->willReturn($cart);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, Argument::any())->shouldNotBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_creates_new_cart_if_there_is_no_identifier_in_storage($storage, $repository, $eventDispatcher, CartInterface $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(null);
        $repository->createNew()->willReturn($cart);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, Argument::any())->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_creates_new_cart_if_identifier_is_wrong($storage, $repository, $eventDispatcher, CartInterface $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(7);
        $repository->find(7)->shouldBeCalled()->willReturn(null);
        $repository->createNew()->willReturn($cart);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, Argument::any())->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    function it_resets_current_cart_identifier_in_storage_when_abandoning_cart($storage, $storage, $eventDispatcher, CartInterface $cart)
    {
        $this->setCart($cart);
        $storage->setCurrentCartIdentifier($cart)->shouldBeCalled();

        $storage->resetCurrentCartIdentifier()->shouldBeCalled();
        $eventDispatcher->dispatch(SyliusCartEvents::CART_ABANDON, Argument::any())->shouldBeCalled();

        $this->abandonCart();
    }

    function it_sets_current_cart_identifier_when_setting_cart($storage, CartInterface $cart)
    {
        $storage->setCurrentCartIdentifier($cart)->shouldBeCalled();

        $this->setCart($cart);
    }

    function it_initializes_cart_while_validating_existence_and_if_there_is_no_identifier_in_storage($storage, $repository, CartInterface $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(null);
        $repository->find(Argument::any())->shouldNotBeCalled();

        $this->hasCart()->shouldReturn(false);
    }

    function it_initializes_cart_while_validating_existence_and_if_there_is_identifier_in_storage($storage, $repository, CartInterface $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(666);
        $repository->find(666)->shouldBeCalled()->willReturn($cart);

        $this->hasCart()->shouldReturn(true);
    }
}
