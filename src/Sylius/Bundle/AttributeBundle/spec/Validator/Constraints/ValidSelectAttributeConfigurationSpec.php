<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AttributeBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Constraint;

final class ValidSelectAttributeConfigurationSpec extends ObjectBehavior
{
    function it_is_a_constraint(): void
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_has_targets(): void
    {
        $this->getTargets()->shouldReturn(Constraint::CLASS_CONSTRAINT);
    }

    function it_is_validated_by_specific_validator(): void
    {
        $this->validatedBy()->shouldReturn('sylius_valid_select_attribute_validator');
    }
}
