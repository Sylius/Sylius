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
use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionActionType;
use Sylius\Bundle\PromotionBundle\Validator\PromotionActionTypeValidator;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class PromotionActionTypeValidatorTest extends TestCase
{
    /** @var ExecutionContextInterface&MockObject */
    private MockObject $context;

    private PromotionActionTypeValidator $promotionActionTypeValidator;

    /** @var PromotionActionInterface&MockObject */
    private PromotionActionInterface $promotionAction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->promotionActionTypeValidator = new PromotionActionTypeValidator(
            [
                'action_one' => 'action_one',
                'action_two' => 'action_two',
            ],
        );
        $this->promotionActionTypeValidator->initialize($this->context);
        $this->promotionAction = $this->createMock(PromotionActionInterface::class);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfPromotionActionType(): void
    {
        /** @var Constraint&MockObject $constraint */
        $constraint = $this->createMock(Constraint::class);

        self::expectException(UnexpectedTypeException::class);

        $this->promotionActionTypeValidator->validate($this->promotionAction, $constraint);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfPromotionAction(): void
    {
        self::expectException(UnexpectedValueException::class);

        $this->promotionActionTypeValidator->validate(new \stdClass(), new PromotionActionType());
    }

    public function testAddsViolationIfPromotionActionHasInvalidType(): void
    {
        /** @var ConstraintViolationBuilderInterface&MockObject $constraintViolationBuilder */
        $constraintViolationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $this->promotionAction->expects(self::once())->method('getType')->willReturn('wrong_type');

        $this->context->expects(self::once())
            ->method('buildViolation')
            ->with('sylius.promotion_action.invalid_type')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())
            ->method('setParameter')
            ->with('{{ available_action_types }}', 'action_one, action_two')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())
            ->method('atPath')
            ->with('type')
            ->willReturn($constraintViolationBuilder);

        $constraintViolationBuilder->expects(self::once())->method('addViolation');

        $this->promotionActionTypeValidator->validate($this->promotionAction, new PromotionActionType());
    }
}
