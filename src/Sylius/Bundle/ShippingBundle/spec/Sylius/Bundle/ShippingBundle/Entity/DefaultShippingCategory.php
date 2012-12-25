<?php

namespace spec\Sylius\Bundle\ShippingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Default shipping category entity spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultShippingCategory extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Entity\DefaultShippingCategory');
    }

    function it_should_be_a_Sylius_shipping_category()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShippingCategoryInterface');
    }

    function it_should_extend_Sylius_shipping_category_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Entity\ShippingCategory');
    }
}
