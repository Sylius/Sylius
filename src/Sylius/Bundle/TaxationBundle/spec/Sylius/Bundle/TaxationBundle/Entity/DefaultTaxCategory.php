<?php

namespace spec\Sylius\Bundle\TaxationBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Default tax category entity spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultTaxCategory extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Entity\DefaultTaxCategory');
    }

    function it_should_be_a_Sylius_tax_category()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Model\TaxCategoryInterface');
    }

    function it_should_extend_Sylius_tax_category_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Entity\TaxCategory');
    }
}
