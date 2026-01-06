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
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionGroupValidator;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionActionGroup;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CatalogPromotionActionGroupValidatorTest extends TestCase
{
    private const VALIDATION_GROUPS = [
        'test' => ['test_group'],
        'another_test' => ['another_test_group'],
    ];

    private CatalogPromotionActionGroupValidator $catalogPromotionActionGroupValidator;

    private ExecutionContextInterface&MockObject $context;

    private CatalogPromotionActionInterface&MockObject $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->catalogPromotionActionGroupValidator = new CatalogPromotionActionGroupValidator(
            self::VALIDATION_GROUPS,
        );
        $this->catalogPromotionActionGroupValidator->initialize($this->context);
        $this->action = $this->createMock(CatalogPromotionActionInterface::class);
    }

    public function testIsConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidator::class, $this->catalogPromotionActionGroupValidator);
    }

    public function testThrowsExceptionWhenConstraintIsNotCatalogPromotionActionGroup(): void
    {
        $constraint = $this->createMock(Constraint::class);

        self::expectException(UnexpectedTypeException::class);

        $this->catalogPromotionActionGroupValidator->validate($this->action, $constraint);
    }

    public function testThrowsExceptionWhenValueIsNotCatalogPromotionAction(): void
    {
        $constraint = new CatalogPromotionActionGroup();

        self::expectException(UnexpectedTypeException::class);

        $this->catalogPromotionActionGroupValidator->validate(new \stdClass(), $constraint);
    }

    public function testDoesNothingWhenTypeIsNull(): void
    {
        $this->action->expects(self::once())->method('getType')->willReturn(null);

        $this->context->expects(self::never())->method('getValidator');

        $this->catalogPromotionActionGroupValidator->validate($this->action, new CatalogPromotionActionGroup());
    }

    public function testDoesNothingWhenTypeIsEmptyString(): void
    {
        $this->action->expects(self::once())->method('getType')->willReturn('');

        $this->context->expects(self::never())->method('getValidator');

        $this->catalogPromotionActionGroupValidator->validate($this->action, new CatalogPromotionActionGroup());
    }

    public function testPassesConfiguredValidationGroups(): void
    {
        $this->action->method('getType')->willReturn('test');

        $validator = $this->createMock(ValidatorInterface::class);
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);
        $violationList = $this->createMock(ConstraintViolationListInterface::class);

        $this->context->method('getValidator')->willReturn($validator);
        $validator->method('inContext')->with($this->context)->willReturn($contextualValidator);

        $contextualValidator
            ->expects($this->once())
            ->method('validate')
            ->with($this->action, null, ['test_group'])
            ->willReturn($contextualValidator);

        $this->context->method('getViolations')->willReturn($violationList);
        $violationList->method('count')->willReturn(1);

        $constraint = new CatalogPromotionActionGroup();

        $this->catalogPromotionActionGroupValidator->validate($this->action, $constraint);
    }
}
