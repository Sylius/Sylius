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
        $this->beConstructedWith('sylius', 'cart', 'SyliusCartBundle:Cart');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Controller\CartController');
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
