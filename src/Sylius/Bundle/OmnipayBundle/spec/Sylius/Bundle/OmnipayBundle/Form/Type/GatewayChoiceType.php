<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OmnipayBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

class GatewayChoiceType extends ObjectBehavior
{
    /**
     * @param array $gateways
     */
    function let(array $gateways)
    {
        $this->beConstructedWith($gateways);
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_should_have_custom_field_name()
    {
        $this->getName()->shouldReturn('sylius_omnipay_gateway_choice');
    }

    function its_getParent_should_return_choice()
    {
        $this->getParent()->shouldReturn('choice');
    }
}
