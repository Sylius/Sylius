<?php

namespace spec\Sylius\Bundle\CartBundle\Controller;

use PHPSpec2\ObjectBehavior;

/**
 * Cart item controller spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartItemController extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('sylius_cart', 'item', 'SyliusCartBundle:CartItem');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Controller\CartItemController');
    }

    function it_should_be_a_controller()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Controller\Controller');
    }

    function it_should_be_Sylius_resource_controller()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceController');
    }

    function it_should_extend_base_Sylius_cart_bundle_controller()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Controller\Controller');
    }
}

