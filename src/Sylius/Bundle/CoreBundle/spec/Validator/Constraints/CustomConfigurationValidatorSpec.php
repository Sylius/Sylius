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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ConstraintResolver;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CustomConfiguration;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CustomConfigurationValidatorSpec extends ObjectBehavior
{
    function let(ConstraintResolver $constraintResolver, ValidatorInterface $validator): void
    {
        $this->beConstructedWith($constraintResolver, $validator);
    }

    function it_is_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_can_validate_custom_configuration_base_on_type(
        ConstraintResolver $constraintResolver,
        ValidatorInterface $validator
    ): void {
        $customConfiguration = [
            'someInteger' => 0,
        ];
        $customConstraint = new Collection([
            'someInteger' => new NotBlank()
        ]);
        $constraintResolver->resolveForType('custom-configuration')->willReturn($customConstraint);
        $validator->validate($customConfiguration, $customConstraint)->shouldBeCalled();

        $this->validate($customConfiguration, new CustomConfiguration());
    }
}
