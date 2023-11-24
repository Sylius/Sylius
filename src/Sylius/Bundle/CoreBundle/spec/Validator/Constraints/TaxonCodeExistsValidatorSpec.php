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
use Sylius\Bundle\CoreBundle\Validator\Constraints\TaxonCodeExists;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class TaxonCodeExistsValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.taxon.code.not_exist';

    function let(TaxonRepositoryInterface $taxonRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($taxonRepository);

        $this->initialize($context);
    }

    function it_throws_an_exception_if_constraint_is_not_an_instance_of_taxon_code_exists(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', ['taxon_code', $constraint])
        ;
    }

    function it_does_nothing_if_value_is_empty(
        TaxonRepositoryInterface $taxonRepository,
        ExecutionContextInterface $context,
    ): void {
        $this->validate('', new TaxonCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
        $taxonRepository->findOneBy(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_nothing_if_taxon_with_given_code_exists(
        TaxonRepositoryInterface $taxonRepository,
        ExecutionContextInterface $context,
        TaxonInterface $taxon,
    ): void {
        $taxonRepository->findOneBy(['code' => 'taxon_code'])->willReturn($taxon);
        $this->validate('taxon_code', new TaxonCodeExists());

        $context->buildViolation(self::MESSAGE)->shouldNotHaveBeenCalled();
    }

    function it_adds_a_violation_if_taxon_with_given_code_does_not_exist(
        TaxonRepositoryInterface $taxonRepository,
        ExecutionContextInterface $context,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $taxonRepository->findOneBy(['code' => 'taxon_code'])->willReturn(null);

        $constraintViolationBuilder->addViolation()->shouldBeCalled();
        $constraintViolationBuilder->atPath(Argument::any())->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->setParameter(Argument::cetera())->willReturn($constraintViolationBuilder);

        $context->buildViolation(self::MESSAGE)->shouldBeCalled()->willReturn($constraintViolationBuilder);

        $this->validate('taxon_code', new TaxonCodeExists());
    }
}
