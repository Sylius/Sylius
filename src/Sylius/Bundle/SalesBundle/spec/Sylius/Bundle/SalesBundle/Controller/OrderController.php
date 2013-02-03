<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SalesBundle\Controller;

use PHPSpec2\ObjectBehavior;

/**
 * Order controller spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderController extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('sylius', 'order', 'SyliusSalesBundle:Order');
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Controller\OrderController');
    }

    function it_should_be_a_controller()
    {
        $this->shouldHaveType('Symfony\Bundle\FrameworkBundle\Controller\Controller');
    }

    function it_should_be_Sylius_resource_controller()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourceController');
    }
}
