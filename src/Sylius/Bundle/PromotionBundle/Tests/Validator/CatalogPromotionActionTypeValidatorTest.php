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
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionTypeValidator;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionActionType;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class CatalogPromotionActionTypeValidatorTest extends TestCase
{
    /** @var ExecutionContextInterface&MockObject */
    private ExecutionContextInterface $context;

    private CatalogPromotionActionTypeValidator $catalogPromotionActionTypeValidator;

    /** @var CatalogPromotionActionInterface&MockObject */
    private CatalogPromotionActionInterface $action;

    private const ACTION_TYPES = [
        'test',
        'another_test',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->catalogPromotionActionTypeValidator = new CatalogPromotionActionTypeValidator(self::ACTION_TYPES);
        $this->catalogPromotionActionTypeValidator->initialize($this->context);
        $this->action = $this->createMock(CatalogPromotionActionInterface::class);
    }

    public function testThrowsExceptionWhenConstraintIsNotCatalogPromotionActionType(): void
    {
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        self::expectException(UnexpectedTypeException::class);

        $this->catalogPromotionActionTypeValidator->validate($this->action, $constraint);
    }

    public function testThrowsExceptionWhenValueIsNotCatalogPromotionAction(): void
    {
        self::expectException(UnexpectedTypeException::class);

        $this->catalogPromotionActionTypeValidator->validate(new \stdClass(), new CatalogPromotionActionType());
    }

    public function testDoesNothingWhenPassedActionHasNullAsType(): void
    {
        $this->action->expects(self::once())->method('getType')->willReturn(null);

        $this->context->expects(self::never())->method('buildViolation');

        $this->catalogPromotionActionTypeValidator->validate($this->action, new CatalogPromotionActionType());
    }

    public function testDoesNothingWhenPassedActionHasAnEmptyStringAsType(): void
    {
        $this->action->expects(self::once())->method('getType')->willReturn('');

        $this->context->expects(self::never())->method('buildViolation');

        $this->catalogPromotionActionTypeValidator->validate($this->action, new CatalogPromotionActionType());
    }

    public function testDoesNothingWhenCatalogPromotionActionHasValidType(): void
    {
        $this->action->expects(self::once())->method('getType')->willReturn('test');

        $this->context->expects(self::never())->method('buildViolation');

        $this->catalogPromotionActionTypeValidator->validate($this->action, new CatalogPromotionActionType());
    }

    public function testAddsViolationWhenTypeIsUnknown(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $violationBuilder */
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $constraint = new CatalogPromotionActionType();

        $this->action->expects(self::once())->method('getType')->willReturn('not_existing_type');

        $this->context->expects(self::once())
            ->method('buildViolation')
            ->with($constraint->invalidType)
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())
            ->method('setParameter')
            ->with('{{ available_action_types }}', implode(', ', self::ACTION_TYPES))
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())
            ->method('atPath')
            ->with('type')
            ->willReturn($violationBuilder);

        $violationBuilder->expects(self::once())->method('addViolation');

        $this->catalogPromotionActionTypeValidator->validate($this->action, $constraint);
    }
}
