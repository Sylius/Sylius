<?php

namespace spec\Sylius\Bundle\CartBundle\Operator;

use PHPSpec2\ObjectBehavior;

/**
 * Cart operator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartOperator extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Manager\ResourceManagerInterface $cartManager
     */
    function let($cartManager)
    {
        $this->beConstructedWith($cartManager);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Operator\CartOperator');
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item
     */
    function it_should_have_fluid_interface($cart, $item)
    {
        $this->addItem($cart, $item)->shouldReturn($this);
        $this->removeItem($cart, $item)->shouldReturn($this);
        $this->refresh($cart)->shouldReturn($this);
        $this->clear($cart)->shouldReturn($this);
        $this->save($cart)->shouldReturn($this);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_calculate_cart_totals_on_refresh($cart)
    {
        $cart->calculateTotal()->shouldBeCalled();

        $this->refresh($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_set_total_items_on_refresh($cart)
    {
        $cart->countItems()->willReturn(3);
        $cart->setTotalItems(3)->shouldBeCalled();

        $this->refresh($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_persist_cart_on_save($cartManager, $cart)
    {
        $cartManager->persist($cart)->shouldBeCalled();

        $this->save($cart);
    }
}
