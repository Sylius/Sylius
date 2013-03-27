<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Controller\CartController');
    }

    function it_is_a_Symfony_controller()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Controller\Controller');
    }

    function it_extends_Sylius_resource_controller()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceController');
    }

    function it_extends_base_Sylius_cart_bundle_controller()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Controller\Controller');
    }
}
