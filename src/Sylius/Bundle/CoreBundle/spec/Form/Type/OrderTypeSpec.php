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

class OrderTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('Order', array('sylius'));
    }

    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\OrderType');
    }

    public function it_should_be_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_should_extend_Sylius_order_form_type()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Form\Type\OrderType');
    }
}
