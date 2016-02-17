<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Constraint;

/**
 * @mixin \Sylius\Bundle\ResourceBundle\Validator\Constraints\Enabled
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class EnabledSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Validator\Constraints\Enabled');
    }

    function it_is_constraint()
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_is_a_property_constraint()
    {
        $this->getTargets()->shouldContain(Constraint::PROPERTY_CONSTRAINT);
    }

    function it_is_a_class_constraint()
    {
        $this->getTargets()->shouldContain(Constraint::CLASS_CONSTRAINT);
    }

    function it_is_validated_by_service()
    {
        $this->validatedBy()->shouldReturn('sylius_resource_enabled_validator');
    }
}
