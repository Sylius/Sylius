<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Form\Type\OrderType;
use Symfony\Component\Form\FormTypeInterface;

class OrderTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Order', ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\OrderType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_should_extend_Sylius_order_form_type()
    {
        $this->shouldHaveType(OrderType::class);
    }
}
