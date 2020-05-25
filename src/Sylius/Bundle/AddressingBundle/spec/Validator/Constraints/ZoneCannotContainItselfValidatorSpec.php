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

namespace spec\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ZoneCannotContainItself;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class ZoneCannotContainItselfValidatorSpec extends ObjectBehavior
{
    function let(ExecutionContextInterface $executionContext): void
    {
        $this->beConstructedWith();
        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_does_nothing_if_value_is_null(ExecutionContextInterface $executionContext): void
    {
        $executionContext->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate(null, new ZoneCannotContainItself());
    }

    function it_throws_an_exception_if_constraint_is_not_of_expected_type(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }])
        ;
    }

    function it_does_not_add_violation_if_zone_does_not_contain_itself_in_members(
        ExecutionContextInterface $executionContext,
        ZoneInterface $zone,
        ZoneMemberInterface $zoneMember
    ): void {
        $zone->getCode()->willReturn('WORLD');
        $zoneMember->getCode()->willReturn('EU');
        $zoneMember->getBelongsTo()->willReturn($zone);

        $executionContext->addViolation(Argument::cetera())->shouldNotBeCalled();

        $this->validate([$zoneMember], new ZoneCannotContainItself());
    }

    function it_adds_violation_if_zone_contains_itself_in_members(
        ExecutionContextInterface $executionContext,
        ZoneInterface $zone,
        ZoneMemberInterface $zoneMember
    ): void {
        $zone->getCode()->willReturn('EU');
        $zoneMember->getCode()->willReturn('EU');
        $zoneMember->getBelongsTo()->willReturn($zone);

        $executionContext->addViolation(Argument::cetera())->shouldBeCalled();

        $this->validate([$zoneMember], new ZoneCannotContainItself());
    }
}
