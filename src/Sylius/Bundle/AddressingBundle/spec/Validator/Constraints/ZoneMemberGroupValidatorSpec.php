<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneMemberGroup;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ZoneMemberGroupValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $context): void
    {
        $this->beConstructedWith(['zone_two' => ['Default', 'zone_two']]);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_zone_member_group(
        Constraint $constraint,
        ZoneMemberInterface $zoneMember,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', [$zoneMember, $constraint])
        ;
    }

    function it_throws_an_exception_if_value_is_not_an_instance_of_zone_member(): void
    {
        $this
            ->shouldThrow(UnexpectedValueException::class)
            ->during('validate', [new \stdClass(), new ZoneMemberGroup()])
        ;
    }

    function it_calls_a_validator_with_group(
        ExecutionContextInterface $context,
        ZoneMemberInterface $zoneMember,
        ZoneInterface $zone,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $zoneMember->getBelongsTo()->willReturn($zone);
        $zone->getType()->willReturn('zone_two');

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($zoneMember, null, ['Default', 'zone_two'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($zoneMember, new ZoneMemberGroup(['groups' => ['Default', 'test_group']]));
    }

    function it_calls_validator_with_default_groups_if_none_provided_for_zone_member_type(
        ExecutionContextInterface $context,
        ZoneMemberInterface $zoneMember,
        ZoneInterface $zone,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $zoneMember->getBelongsTo()->willReturn($zone);
        $zone->getType()->willReturn('zone_one');

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($zoneMember, null, ['Default', 'test_group'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($zoneMember, new ZoneMemberGroup(['groups' => ['Default', 'test_group']]));
    }

    function it_calls_validator_with_default_groups_if_zone_is_null(
        ExecutionContextInterface $context,
        ZoneMemberInterface $zoneMember,
        ValidatorInterface $validator,
        ContextualValidatorInterface $contextualValidator,
    ): void {
        $zoneMember->getBelongsTo()->willReturn(null);

        $context->getValidator()->willReturn($validator);
        $validator->inContext($context)->willReturn($contextualValidator);

        $contextualValidator->validate($zoneMember, null, ['Default', 'test_group'])->willReturn($contextualValidator)->shouldBeCalled();

        $this->validate($zoneMember, new ZoneMemberGroup(['groups' => ['Default', 'test_group']]));
    }
}
