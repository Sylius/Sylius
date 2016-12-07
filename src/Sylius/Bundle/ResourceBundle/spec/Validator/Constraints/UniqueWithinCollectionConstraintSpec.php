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
use Sylius\Bundle\ResourceBundle\Validator\Constraints\UniqueWithinCollectionConstraint;
use Sylius\Bundle\ResourceBundle\Validator\UniqueWithinCollectionConstraintValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class UniqueWithinCollectionConstraintSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UniqueWithinCollectionConstraint::class);
    }

    function it_extends_symfony_constraint_class()
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_is_validate_by_unique_field_during_creation_validator()
    {
        $this->validatedBy()->shouldReturn(UniqueWithinCollectionConstraintValidator::class);
    }
}
