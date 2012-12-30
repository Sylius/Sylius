<?php

namespace spec\Sylius\Bundle\SalesBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Adjustment default entity spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultAdjustment extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Entity\DefaultAdjustment');
    }

    function it_should_be_Sylius_adjustment()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\AdjustmentInterface');
    }

    function it_should_extend_Sylius_adjustment_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Entity\Adjustment');
    }
}
