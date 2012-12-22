<?php

namespace spec\Sylius\Bundle\TaxationBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Tax rate entity.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxRate extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Entity\TaxRate');
    }

    function it_should_be_a_Sylius_tax_rate()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Model\TaxRateInterface');
    }

    function it_should_extend_Sylius_tax_rate_model()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Model\TaxRate');
    }
}
