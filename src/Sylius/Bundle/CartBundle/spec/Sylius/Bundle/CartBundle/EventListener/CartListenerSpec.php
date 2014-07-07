<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Bundle\CartBundle\Event\CartEvent;
use Sylius\Bundle\CartBundle\Event\CartItemEvent;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Fran Moreno <franmomu@gmail.com>
 */
class CartListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $manager, ValidatorInterface $validator, CartProviderInterface $provider)
    {
        $this->beConstructedWith($manager, $validator, $provider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\CartListener');
    }

    function it_should_add_a_item_to_a_cart_from_event(CartItemEvent $event, CartInterface $cart, CartItemInterface $cartItem)
    {
        $event->getCart()->willReturn($cart);
        $event->getItem()->willReturn($cartItem);
        $cart->addItem($cartItem)->shouldBeCalled();

        $this->addItem($event);
    }

    function it_should_remove_a_item_to_a_cart_from_event(CartItemEvent $event, CartInterface $cart, CartItemInterface $cartItem)
    {
        $event->getCart()->willReturn($cart);
        $event->getItem()->willReturn($cartItem);
        $cart->removeItem($cartItem)->shouldBeCalled();

        $this->removeItem($event);
    }

    function it_should_clear_a_cart_from_event(CartEvent $event, CartInterface $cart, $manager, $provider)
    {
        $event->getCart()->willReturn($cart);
        $manager->remove($cart)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();
        $provider->abandonCart()->shouldBeCalled();

        $this->clearCart($event);
    }

    function it_should_save_a_valid_cart($manager, $provider, CartEvent $event, CartInterface $cart)
    {
        $event->getCart()->willReturn($cart);
        $manager->persist($cart)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();
        $provider->setCart($cart)->shouldBeCalled();

        $this->saveCart($event);
    }

    function it_should_not_save_an_invalid_cart(
        $manager,
        $provider,
        $validator,
        CartEvent $event,
        CartInterface $cart,
        ConstraintViolationListInterface $constraintList
    ) {
        $constraintList->count()->willReturn(1);
        $event->getCart()->willReturn($cart);
        $validator->validate($cart)->shouldBeCalled()->willReturn($constraintList);

        $manager->persist($cart)->shouldNotBeCalled();
        $manager->flush()->shouldNotBeCalled();
        $provider->setCart($cart)->shouldNotBeCalled();

        $this->saveCart($event);
    }
}
