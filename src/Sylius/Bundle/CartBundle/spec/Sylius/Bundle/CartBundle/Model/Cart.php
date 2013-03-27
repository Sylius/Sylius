<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Cart spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Cart extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Model\Cart');
    }

    function it_implements_Sylius_cart_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Model\CartInterface');
    }

    function it_creates_items_collection_by_default()
    {
        $this->getItems()->shouldHaveType('Doctrine\\Common\\Collections\\Collection');
    }

    function it_has_0_total_quantity_by_default()
    {
        $this->getTotalQuantity()->shouldReturn(0);
    }

    function it_has_0_total_items_by_default()
    {
        $this->getTotalItems()->shouldReturn(0);
    }

    function it_has_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_is_empty_by_default()
    {
        $this->countItems()->shouldReturn(0);
        $this->shouldBeEmpty();
    }

    function it_should_not_be_locked_by_default()
    {
        $this->shouldNotBeLocked();
    }

    function it_can_be_locked()
    {
        $this->setLocked(true);
        $this->shouldBeLocked();
    }

    function it_is_not_expired_by_default()
    {
        $this->shouldNotBeExpired();
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item
     */
    function it_has_fluent_interface_for_items_management($item)
    {
        $this->addItem($item)->shouldReturn($this);
        $this->removeItem($item)->shouldReturn($this);

        $this->clearItems()->shouldReturn($this);
    }

    function it_throws_exception_when_total_quantity_is_less_than_0()
    {
        $this
            ->shouldThrow(new \OutOfRangeException('Total quantity must not be less than 0'))
            ->duringSetTotalQuantity(-1)
        ;
    }

    function it_throws_exception_when_total_items_is_less_than_0()
    {
        $this
            ->shouldThrow(new \OutOfRangeException('Total items must not be less than 0'))
            ->duringSetTotalItems(-1)
        ;
    }

    function it_sets_total_items_to_0_if_change_is_bigger_than_current_amount()
    {
        $this->setTotalItems(5);
        $this->changeTotalItems(-10);

        $this->getTotalItems()->shouldReturn(0);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item1
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item2
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item3
     */
    function it_calculates_correct_total($item1, $item2, $item3)
    {
        $item1->getTotal()->willReturn(29999);
        $item2->getTotal()->willReturn(45000);
        $item3->getTotal()->willReturn(250);

        $item1->equals(ANY_ARGUMENT)->willReturn(false);
        $item2->equals(ANY_ARGUMENT)->willReturn(false);
        $item3->equals(ANY_ARGUMENT)->willReturn(false);

        $this
            ->addItem($item1)
            ->addItem($item2)
            ->addItem($item3)
        ;

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(75249);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item1
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item2
     */
    function it_sums_the_quantities_of_equal_items($item1, $item2)
    {
        $item1->getQuantity()->willReturn(3);
        $item2->getQuantity()->willReturn(7);

        $item1->equals($item2)->willReturn(true);

        $this
            ->addItem($item1)
            ->addItem($item2)
        ;

        $this->countItems()->shouldReturn(1);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item
     */
    function it_adds_items($item)
    {
        $this->hasItem($item)->shouldReturn(false);
        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item
     */
    function it_removes_items($item)
    {
        $this->addItem($item);
        $this->removeItem($item);
        $this->hasItem($item)->shouldReturn(false);

        $this->shouldBeEmpty();
    }

    function it_is_not_expired_if_the_expiration_time_is_in_future()
    {
        $expiresAt = new \DateTime('tomorrow');
        $this->setExpiresAt($expiresAt);

        $this->shouldNotBeExpired();
    }

    function it_is_expired_if_the_expiration_time_is_in_past()
    {
        $expiresAt = new \DateTime('-1 hour');
        $this->setExpiresAt($expiresAt);

        $this->shouldBeExpired();
    }
}
