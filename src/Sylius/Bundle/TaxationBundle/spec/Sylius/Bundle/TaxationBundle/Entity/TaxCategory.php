<?php

namespace spec\Sylius\Bundle\TaxationBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Tax category entity.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxCategory extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Entity\TaxCategory');
    }

    function it_should_be_a_Sylius_tax_category()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface');
    }

    function it_should_extend_Sylius_tax_category_model()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Model\TaxCategory');
    }
}
