<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    function it_should_implement_Sylius_adjustment_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\AdjustmentInterface');
    }

    function it_should_extend_Sylius_adjustment_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Entity\Adjustment');
    }
}
