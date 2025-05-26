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
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeGroupValidator;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionScopeGroup;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Validator\ContextualValidatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CatalogPromotionScopeGroupValidatorTest extends TestCase
{
    private const VALIDATION_GROUPS = [
        'test' => ['test_group'],
        'another_test' => ['another_test_group'],
    ];

    private CatalogPromotionScopeGroupValidator $catalogPromotionScopeGroupValidator;

    private ExecutionContextInterface&MockObject $context;

    private CatalogPromotionScopeInterface&MockObject $scope;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->catalogPromotionScopeGroupValidator = new CatalogPromotionScopeGroupValidator(
            self::VALIDATION_GROUPS,
        );
        $this->catalogPromotionScopeGroupValidator->initialize($this->context);
        $this->scope = $this->createMock(CatalogPromotionScopeInterface::class);
    }

    public function testItIsAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidator::class, $this->catalogPromotionScopeGroupValidator);
    }

    public function testThrowsExceptionWhenConstraintIsNotCatalogPromotionScopeGroup(): void
    {
        $constraint = $this->createMock(Constraint::class);

        self::expectException(UnexpectedTypeException::class);

        $this->catalogPromotionScopeGroupValidator->validate($this->scope, $constraint);
    }

    public function testThrowsExceptionWhenValueIsNotCatalogPromotionScope(): void
    {
        $constraint = new CatalogPromotionScopeGroup();

        self::expectException(UnexpectedTypeException::class);

        $this->catalogPromotionScopeGroupValidator->validate(new \stdClass(), $constraint);
    }

    public function testDoesNothingWhenTypeIsNull(): void
    {
        $this->scope->expects(self::once())->method('getType')->willReturn(null);

        $this->context->expects(self::never())->method('getValidator');

        $this->catalogPromotionScopeGroupValidator->validate($this->scope, new CatalogPromotionScopeGroup());
    }

    public function testDoesNothingWhenTypeIsEmptyString(): void
    {
        $this->scope->expects(self::once())->method('getType')->willReturn('');

        $this->context->expects(self::never())->method('getValidator');

        $this->catalogPromotionScopeGroupValidator->validate($this->scope, new CatalogPromotionScopeGroup());
    }

    public function testPassesConfiguredValidationGroupsForFurtherValidation(): void
    {
        $scope = $this->createMock(CatalogPromotionScopeInterface::class);
        $scope->method('getType')->willReturn('test');

        $constraint = new CatalogPromotionScopeGroup();

        $validator = $this->createMock(ValidatorInterface::class);
        $contextualValidator = $this->createMock(ContextualValidatorInterface::class);
        $violationList = $this->createMock(ConstraintViolationListInterface::class);

        $this->context->method('getValidator')->willReturn($validator);
        $validator->method('inContext')->with($this->context)->willReturn($contextualValidator);

        $contextualValidator
            ->expects($this->once())
            ->method('validate')
            ->with($scope, null, ['test_group'])
            ->willReturn($contextualValidator);

        $this->context->method('getViolations')->willReturn($violationList);
        $violationList->method('count')->willReturn(1);

        $this->catalogPromotionScopeGroupValidator->validate($scope, $constraint);
    }
}
