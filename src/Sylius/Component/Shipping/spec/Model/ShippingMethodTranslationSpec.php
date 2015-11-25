<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ShippingMethodTranslationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Model\ShippingMethodTranslation');
    }

    function it_implements_Sylius_shipping_method_interface()
    {
        $this->shouldImplement('Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Shippable goods');
        $this->getName()->shouldReturn('Shippable goods');
    }

    function it_returns_name_when_converted_to_string()
    {
        $this->setName('Shippable goods');
        $this->__toString()->shouldReturn('Shippable goods');
    }
}
