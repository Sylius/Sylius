<?php

namespace spec\Sylius\Bundle\CartBundle\Controller;

use PHPSpec2\ObjectBehavior;

/**
 * Cart controller spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartController extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('sylius_cart', 'cart', 'SyliusCartBundle:Cart');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Controller\CartController');
    }

    function it_should_be_sylius_resource_controller()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceController');
    }
}
