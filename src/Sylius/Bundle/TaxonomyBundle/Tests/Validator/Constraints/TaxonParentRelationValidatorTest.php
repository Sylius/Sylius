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

namespace Sylius\Bundle\TaxonomyBundle\Tests\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\TaxonomyBundle\Validator\Constraints\TaxonParentRelation;
use Sylius\Bundle\TaxonomyBundle\Validator\Constraints\TaxonParentRelationValidator;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class TaxonParentRelationValidatorTest extends TestCase
{
    private TaxonParentRelationValidator $validator;

    private ExecutionContextInterface|MockObject $context;

    private TaxonInterface|MockObject $taxon;

    private TaxonInterface|MockObject $parent;

    private ConstraintViolationBuilderInterface|MockObject $builder;

    private TaxonInterface|MockObject $grandParent;

    protected function setUp(): void
    {
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new TaxonParentRelationValidator();
        $this->validator->initialize($this->context);
        $this->taxon = $this->createMock(TaxonInterface::class);
        $this->parent = $this->createMock(TaxonInterface::class);
        $this->builder = $this->createMock(ConstraintViolationBuilderInterface::class);
        $this->grandParent = $this->createMock(TaxonInterface::class);
    }

    public function testDoesNothingWhenValueIsNotTaxon(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $constraint = new TaxonParentRelation(['message' => 'Invalid relation']);
        $this->validator->validate('not_a_taxon', $constraint);
    }

    public function testDoesNothingWhenNoParent(): void
    {
        $this->taxon->method('getParent')->willReturn(null);

        $this->context->expects($this->never())->method('buildViolation');

        $constraint = new TaxonParentRelation(['message' => 'Invalid relation']);
        $this->validator->validate($this->taxon, $constraint);
    }

    public function testBuildsViolationWhenParentIsSameInstance(): void
    {
        $this->taxon->method('getParent')->willReturn($this->taxon);

        $this->builder->expects($this->once())->method('atPath')->with('parent')->willReturnSelf();
        $this->builder->expects($this->once())->method('addViolation');

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with('Invalid relation')
            ->willReturn($this->builder);

        $constraint = new TaxonParentRelation(['message' => 'Invalid relation']);
        $this->validator->validate($this->taxon, $constraint);
    }

    public function testBuildsViolationWhenParentHasSameId(): void
    {
        $this->parent->method('getId')->willReturn(1);
        $this->parent->method('getParent')->willReturn(null);

        $this->taxon->method('getParent')->willReturn($this->parent);
        $this->taxon->method('getId')->willReturn(1);

        $this->builder->expects($this->once())->method('atPath')->with('parent')->willReturnSelf();
        $this->builder->expects($this->once())->method('addViolation');

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with('Invalid relation')
            ->willReturn($this->builder);

        $constraint = new TaxonParentRelation(['message' => 'Invalid relation']);
        $this->validator->validate($this->taxon, $constraint);
    }

    public function testBuildsViolationWhenParentIsInChildHierarchy(): void
    {
        $this->taxon->method('getParent')->willReturn($this->parent);
        $this->taxon->method('getId')->willReturn(1);

        $this->parent->method('getId')->willReturn(2);
        $this->parent->method('getParent')->willReturn($this->grandParent);

        $this->grandParent->method('getParent')->willReturn($this->taxon);

        $this->builder->expects($this->once())->method('atPath')->with('parent')->willReturnSelf();
        $this->builder->expects($this->once())->method('addViolation');

        $this->context
            ->expects($this->once())
            ->method('buildViolation')
            ->with('Invalid relation')
            ->willReturn($this->builder);

        $constraint = new TaxonParentRelation(['message' => 'Invalid relation']);
        $this->validator->validate($this->taxon, $constraint);
    }

    public function testValidRelationDoesNotTriggerViolation(): void
    {
        $this->taxon->method('getId')->willReturn(1);
        $this->parent->method('getId')->willReturn(2);
        $this->grandParent->method('getId')->willReturn(3);

        $this->taxon->method('getParent')->willReturn($this->parent);
        $this->parent->method('getParent')->willReturn($this->grandParent);
        $this->grandParent->method('getParent')->willReturn(null);

        $this->context->expects($this->never())->method('buildViolation');

        $constraint = new TaxonParentRelation(['message' => 'Invalid relation']);
        $this->validator->validate($this->taxon, $constraint);
    }
}
