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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Promotion\PromotionCoupon;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Resolver\UriTemplateParentResourceResolverInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Admin\Promotion\PromotionCoupon\PersistProcessor;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $processor;

    private MockObject&UriTemplateParentResourceResolverInterface $uriTemplateParentResourceResolver;

    private MockObject&ValidatorInterface $validator;

    private PersistProcessor $persistProcessor;

    protected function setUp(): void
    {
        $this->processor = $this->createMock(ProcessorInterface::class);
        $this->uriTemplateParentResourceResolver = $this->createMock(UriTemplateParentResourceResolverInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->persistProcessor = new PersistProcessor(
            $this->processor,
            $this->uriTemplateParentResourceResolver,
            $this->validator,
        );
    }

    public function testAProcessorInterface(): void
    {
        self::assertInstanceOf(ProcessorInterface::class, $this->persistProcessor);
    }

    public function testProcessesAPromotionCouponIfOperationIsNotPost(): void
    {
        /** @var Operation|MockObject $operationMock */
        $operationMock = $this->createMock(Operation::class);
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);

        $this->processor->expects(self::once())->method('process')->with($promotionCouponMock, $operationMock, [], []);

        $this->uriTemplateParentResourceResolver->expects(self::never())->method('resolve')->with($this->any());

        $this->validator->expects(self::never())->method('validate')->with($this->any());

        $this->persistProcessor->process($promotionCouponMock, $operationMock, [], []);
    }

    public function testProcessesAPromotionCouponIfOperationIsPost(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        /** @var PromotionInterface|MockObject $promotionMock */
        $promotionMock = $this->createMock(PromotionInterface::class);
        /** @var ConstraintViolationListInterface|MockObject $constraintViolationListMock */
        $constraintViolationListMock = $this->createMock(ConstraintViolationListInterface::class);

        $operation = new Post(validationContext: ['groups' => ['sylius']]);

        $this->uriTemplateParentResourceResolver->expects(self::once())
            ->method('resolve')
            ->with($promotionCouponMock, $operation, [])
            ->willReturn($promotionMock);

        $this->validator->expects(self::once())
            ->method('validate')
            ->with($promotionCouponMock, null, ['sylius'])
            ->willReturn($constraintViolationListMock);

        $constraintViolationListMock->expects(self::once())->method('count')->willReturn(0);

        $this->processor->expects(self::once())->method('process')->with($promotionCouponMock, $operation, [], []);

        $this->persistProcessor->process($promotionCouponMock, $operation, [], []);
    }

    public function testThrowsAValidationExceptionIfThereAreViolations(): void
    {
        /** @var PromotionCouponInterface|MockObject $promotionCouponMock */
        $promotionCouponMock = $this->createMock(PromotionCouponInterface::class);
        /** @var PromotionInterface|MockObject $promotionMock */
        $promotionMock = $this->createMock(PromotionInterface::class);
        /** @var ConstraintViolationListInterface|MockObject $constraintViolationListMock */
        $constraintViolationListMock = $this->createMock(ConstraintViolationListInterface::class);

        $operation = new Post(validationContext: ['groups' => ['sylius']]);

        $this->uriTemplateParentResourceResolver->expects(self::once())
            ->method('resolve')
            ->with($promotionCouponMock, $operation, [])
            ->willReturn($promotionMock);

        $this->validator->expects(self::once())
            ->method('validate')
            ->with($promotionCouponMock, null, ['sylius'])
            ->willReturn($constraintViolationListMock);

        $constraintViolationListMock->expects(self::once())->method('count')->willReturn(1);

        $constraintViolationListMock->expects(self::once())->method('rewind');

        $constraintViolationListMock->expects(self::once())->method('valid')->willReturn(false);

        $this->processor->expects(self::never())->method('process')->with($promotionCouponMock, $operation, [], []);

        $this->expectException(ValidationException::class);

        $this->persistProcessor->process($promotionCouponMock, $operation, [], []);
    }
}
