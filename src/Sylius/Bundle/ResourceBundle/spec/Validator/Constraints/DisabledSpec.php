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

namespace spec\Sylius\Bundle\ResourceBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Validator\DisabledValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @author Kamil Kokot <kamil@kokot.me>
 */
final class DisabledSpec extends ObjectBehavior
{
    function it_is_constraint(): void
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_is_a_property_constraint(): void
    {
        $this->getTargets()->shouldContain(Constraint::PROPERTY_CONSTRAINT);
    }

    function it_is_a_class_constraint(): void
    {
        $this->getTargets()->shouldContain(Constraint::CLASS_CONSTRAINT);
    }

    function it_is_validated_by_disabled_validator(): void
    {
        $this->validatedBy()->shouldReturn(DisabledValidator::class);
    }
}
