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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\TaxonCodeExists;
use Sylius\Bundle\CoreBundle\Validator\Constraints\TaxonCodeExistsValidator;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class TaxonCodeExistsValidatorTest extends TestCase
{
    private MockObject&TaxonRepositoryInterface $taxonRepository;

    private ExecutionContextInterface&MockObject $context;

    private TaxonCodeExistsValidator $validator;

    protected function setUp(): void
    {
        $this->taxonRepository = $this->createMock(TaxonRepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new TaxonCodeExistsValidator($this->taxonRepository);
        $this->validator->initialize($this->context);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidator::class, $this->validator);
    }

    public function testItThrowsExceptionIfConstraintIsInvalid(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('taxon_code', $this->createMock(Constraint::class));
    }

    public function testItDoesNothingIfValueIsEmpty(): void
    {
        $this->taxonRepository->expects($this->never())->method('findOneBy');
        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('', new TaxonCodeExists());
    }

    public function testItDoesNothingIfTaxonWithCodeExists(): void
    {
        $taxon = $this->createMock(TaxonInterface::class);

        $this->taxonRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => 'taxon_code'])
            ->willReturn($taxon)
        ;

        $this->context->expects($this->never())->method('buildViolation');

        $this->validator->validate('taxon_code', new TaxonCodeExists());
    }

    public function testItAddsViolationIfTaxonWithCodeDoesNotExist(): void
    {
        $constraint = new TaxonCodeExists();
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->taxonRepository
            ->method('findOneBy')
            ->with(['code' => 'taxon_code'])
            ->willReturn(null)
        ;

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violationBuilder)
        ;

        $violationBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('{{ code }}', 'taxon_code')
            ->willReturn($violationBuilder)
        ;

        $violationBuilder->expects($this->once())->method('addViolation');

        $this->validator->validate('taxon_code', $constraint);
    }
}
