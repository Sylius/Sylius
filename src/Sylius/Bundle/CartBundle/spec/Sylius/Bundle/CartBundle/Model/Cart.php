<?php

namespace spec\Sylius\Bundle\CartBundle\Model;

use PHPSpec2\ObjectBehavior;

/**
 * Cart spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Cart extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Model\Cart');
    }

    function it_should_have_proper_default_values()
    {
        $this->getItems()->shouldHaveType('Doctrine\\Common\\Collections\\Collection');
        $this->getTotalItems()->shouldReturn(0);
        $this->getTotal()->shouldReturn(0);
        $this->countItems()->shouldReturn(0);
        $this->shouldBeEmpty();
        $this->shouldNotBeLocked();
        $this->getExpiresAt()->shouldHaveType('DateTime');
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item
     */
    function it_should_have_fluid_interface($item)
    {
        $this->addItem($item)->shouldReturn($this);
        $this->removeItem($item)->shouldReturn($this);

        $this->clearItems()->shouldReturn($this);
    }

    function it_should_complain_when_total_items_is_less_than_0()
    {
        $this
            ->shouldThrow(new \OutOfRangeException('Total items must not be less than 0'))
            ->duringSetTotalItems(-1)
        ;
    }

    function it_should_reset_total_items_to_0_if_change_is_bigger_than_current_amount()
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
    function it_should_calculate_correct_total($item1, $item2, $item3)
    {
        $item1->getTotal()->willReturn(299.99);
        $item2->getTotal()->willReturn(450);
        $item3->getTotal()->willReturn(2.50);

        $item1->equals($item2)->willReturn(false);
        $item1->equals($item3)->willReturn(false);
        $item2->equals($item3)->willReturn(false);

        $this
            ->addItem($item1)
            ->addItem($item2)
            ->addItem($item3)
        ;

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(752.49);
    }

    function it_should_be_empty_if_no_items_inside()
    {
        $this->shouldBeEmpty();
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item
     */
    function it_should_add_and_remove_items_properly($item)
    {
        $this->hasItem($item)->shouldReturn(false);

        $this->addItem($item);
        $this->hasItem($item)->shouldReturn(true);

        $this->removeItem($item);
        $this->hasItem($item)->shouldReturn(false);

        $this->shouldBeEmpty();
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item1
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item2
     */
    function it_should_sum_the_quantities_of_equal_items($item1, $item2)
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

    function it_should_be_not_expired_by_default()
    {
        $this->shouldNotBeExpired();
    }

    function it_should_be_expired_if_the_expiration_time_is_in_past()
    {
        $expiresAt = new \DateTime('-1 hour');
        $this->setExpiresAt($expiresAt);

        $this->shouldBeExpired();
    }
}
