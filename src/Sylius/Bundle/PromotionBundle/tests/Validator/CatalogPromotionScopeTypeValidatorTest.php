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

namespace Tests\Sylius\Bundle\PromotionBundle\Validator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeTypeValidator;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScopeType;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CatalogPromotionScopeTypeValidatorTest extends TestCase
{
    /** @var ExecutionContextInterface&MockObject */
    private ExecutionContextInterface $context;

    private CatalogPromotionScopeTypeValidator $catalogPromotionScopeTypeValidator;

    /** @var CatalogPromotionScopeInterface&MockObject */
    private CatalogPromotionScopeInterface $scope;

    private const SCOPE_TYPES = [
        'test',
        'another_test',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->catalogPromotionScopeTypeValidator = new CatalogPromotionScopeTypeValidator(self::SCOPE_TYPES);
        $this->catalogPromotionScopeTypeValidator->initialize($this->context);
        $this->scope = $this->createMock(CatalogPromotionScopeInterface::class);
    }

    public function testThrowsExceptionWhenConstraintIsNotCatalogPromotionScopeType(): void
    {
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        self::expectException(UnexpectedTypeException::class);

        $this->catalogPromotionScopeTypeValidator->validate($this->scope, $constraint);
    }

    public function testThrowsExceptionWhenValueIsNotCatalogPromotionScope(): void
    {
        self::expectException(UnexpectedTypeException::class);

        $this->catalogPromotionScopeTypeValidator->validate(new \stdClass(), new CatalogPromotionScopeType());
    }

    public function testDoesNothingWhenPassedScopeHasNullAsType(): void
    {
        $this->scope->expects(self::once())->method('getType')->willReturn(null);

        $this->context->expects(self::never())->method('buildViolation');

        $this->catalogPromotionScopeTypeValidator->validate($this->scope, new CatalogPromotionScopeType());
    }

    public function testDoesNothingWhenPassedScopeHasAnEmptyStringAsType(): void
    {
        $this->scope->expects(self::once())->method('getType')->willReturn('');

        $this->context->expects(self::never())->method('buildViolation');

        $this->catalogPromotionScopeTypeValidator->validate($this->scope, new CatalogPromotionScopeType());
    }

    public function testDoesNothingWhenCatalogPromotionScopeHasValidType(): void
    {
        $this->scope->expects(self::once())->method('getType')->willReturn('test');

        $this->context->expects(self::never())->method('buildViolation');

        $this->catalogPromotionScopeTypeValidator->validate($this->scope, new CatalogPromotionScopeType());
    }

    public function testAddsViolationWhenTypeIsUnknown(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $violationBuilder */
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraint = new CatalogPromotionScopeType();

        $this->scope->expects(self::once())->method('getType')->willReturn('not_existing_type');

        $this->context->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->invalidType)
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())
            ->method('setParameter')
            ->with('{{ available_scope_types }}', implode(', ', self::SCOPE_TYPES))
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())
            ->method('atPath')
            ->with('type')
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())->method('addViolation');

        $this->catalogPromotionScopeTypeValidator->validate($this->scope, $constraint);
    }
}
