<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AttributeBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AttributeBundle\Validator\Constraints\ValidAttributeValue;
use Symfony\Component\Validator\Constraint;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ValidAttributeValueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ValidAttributeValue::class);
    }

    function it_is_constraint()
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_has_targets()
    {
        $this->getTargets()->shouldReturn(Constraint::CLASS_CONSTRAINT);
    }

    function it_is_validated_by_specific_validator()
    {
        $this->validatedBy()->shouldReturn('sylius_valid_attribute_value_validator');
    }
}
