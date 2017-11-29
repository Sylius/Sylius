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
use Sylius\Bundle\ResourceBundle\Validator\UniqueWithinCollectionConstraintValidator;
use Symfony\Component\Validator\Constraint;

final class UniqueWithinCollectionConstraintSpec extends ObjectBehavior
{
    public function it_extends_symfony_constraint_class(): void
    {
        $this->shouldHaveType(Constraint::class);
    }

    public function it_is_validate_by_unique_field_during_creation_validator(): void
    {
        $this->validatedBy()->shouldReturn(UniqueWithinCollectionConstraintValidator::class);
    }
}
