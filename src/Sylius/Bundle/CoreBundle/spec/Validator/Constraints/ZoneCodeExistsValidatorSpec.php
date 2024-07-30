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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ZoneCodeExists;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class ZoneCodeExistsValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.zone.code.not_exist';

    function let(RepositoryInterface $zoneRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($zoneRepository);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_zone_code_exists(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', ['zone_code', $constraint])
        ;
    }

    function it_does_nothing_if_value_is_empty(
        RepositoryInterface $zoneRepository,
        ExecutionContextInterface $context,
    ): void {
        $this->validate('', new ZoneCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
        $zoneRepository->findOneBy(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_nothing_if_zone_with_given_code_exists(
        RepositoryInterface $zoneRepository,
        ExecutionContextInterface $context,
        ZoneInterface $zone,
    ): void {
        $zoneRepository->findOneBy(['code' => 'zone_code'])->willReturn($zone);
        $this->validate('zone_code', new ZoneCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_adds_a_violation_if_zone_with_given_code_does_not_exist(
        RepositoryInterface $zoneRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $zoneRepository->findOneBy(['code' => 'zone_code'])->willReturn(null);

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation(self::MESSAGE)->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate('zone_code', new ZoneCodeExists());
    }
}
