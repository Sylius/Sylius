<?php

namespace spec\Sylius\Bundle\ShippingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Shipping category mapped super-class spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingCategory extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Entity\ShippingCategory');
    }

    function it_should_be_a_Sylius_shipping_category()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface');
    }

    function it_should_extend_Sylius_shipping_category_model()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\ShippingCategory');
    }
}
