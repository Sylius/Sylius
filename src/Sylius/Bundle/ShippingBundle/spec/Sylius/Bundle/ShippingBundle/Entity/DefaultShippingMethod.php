<?php

namespace spec\Sylius\Bundle\ShippingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Default shipping method entity spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultShippingMethod extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Entity\DefaultShippingMethod');
    }

    function it_should_be_a_Sylius_shipping_method()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface');
    }

    function it_should_extend_Sylius_shipping_method_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Entity\ShippingMethod');
    }
}
