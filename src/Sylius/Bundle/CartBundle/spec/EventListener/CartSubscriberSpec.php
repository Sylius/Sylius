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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Context\CartContextInterface;
use Sylius\Component\Cart\Event\CartEvent;
use Sylius\Component\Cart\Event\CartItemEvent;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Fran Moreno <franmomu@gmail.com>
 */
class CartSubscriberSpec extends ObjectBehavior
{
    function let(ObjectManager $manager, ValidatorInterface $validator, OrderItemQuantityModifierInterface $orderItemQuantityModifier)
    {
        $this->beConstructedWith($manager, $validator, $orderItemQuantityModifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\EventListener\CartSubscriber');
    }

    function it_adds_an_item_to_a_cart_from_event_if_does_not_already_exist(
        CartItemEvent $event,
        CartInterface $cart,
        CartItemInterface $cartItem,
        Collection $items,
        OrderItemInterface $existingItem,
        \Iterator $iterator
    ) {
        $event->getCart()->willReturn($cart);
        $event->getItem()->willReturn($cartItem);

        $cart->getItems()->willReturn($items);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false);
        $iterator->current()->willReturn($existingItem);
        $iterator->next()->shouldBeCalled();

        $cartItem->equals($existingItem)->willReturn(false);

        $cart->addItem($cartItem)->shouldBeCalled();

        $this->addItem($event);
    }

    function it_merges_cart_items_if_equal(
        $orderItemQuantityModifier,
        CartItemEvent $event,
        CartInterface $cart,
        CartItemInterface $cartItem,
        Collection $items,
        OrderItemInterface $existingItem,
        \Iterator $iterator
    ) {
        $event->getCart()->willReturn($cart);
        $event->getItem()->willReturn($cartItem);

        $cart->getItems()->willReturn($items);
        $items->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, false);
        $iterator->current()->willReturn($existingItem);

        $cartItem->equals($existingItem)->willReturn(true);

        $cartItem->getQuantity()->willReturn(3);
        $existingItem->getQuantity()->willReturn(1);

        $orderItemQuantityModifier->modify($existingItem, 4)->shouldBeCalled();

        $this->addItem($event);
    }

    function it_should_remove_a_item_to_a_cart_from_event(CartItemEvent $event, CartInterface $cart, CartItemInterface $cartItem)
    {
        $event->getCart()->willReturn($cart);
        $event->getItem()->willReturn($cartItem);
        $cart->removeItem($cartItem)->shouldBeCalled();

        $this->removeItem($event);
    }

    function it_should_clear_a_cart_from_event(CartEvent $event, CartInterface $cart, $manager)
    {
        $event->getCart()->willReturn($cart);
        $manager->remove($cart)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $this->clearCart($event);
    }

    function it_should_save_a_valid_cart($manager, CartEvent $event, CartInterface $cart)
    {
        $event->getCart()->willReturn($cart);
        $manager->persist($cart)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $this->saveCart($event);
    }

    function it_should_not_save_an_invalid_cart(
        $manager,
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

        $this->saveCart($event);
    }
}
