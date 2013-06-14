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
 * Cart item spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartItem extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Model\CartItem');
    }

    function it_implements_Sylius_cart_item_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Model\CartItemInterface');
    }

    function it_has_quantity_equal_to_1_by_default()
    {
        $this->getQuantity()->shouldReturn(1);
    }

    function it_has_unit_price_equal_to_0_by_default()
    {
        $this->getUnitPrice()->shouldReturn(0);
    }

    function it_has_total_equal_to_0_by_default()
    {
        $this->getTotal()->shouldReturn(0);
    }

    function it_calculates_correct_total()
    {
        $this->setQuantity(13);
        $this->setUnitPrice(1499);

        $this->calculateTotal();

        $this->getTotal()->shouldReturn(19487);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $otherCartItem
     */
    function it_does_not_recognize_items_as_equal_if_they_do_not_have_the_same_id($otherCartItem)
    {
        $otherCartItem->getId()->willReturn(1);

        $this->equals($otherCartItem)->shouldReturn(false);
    }
}
