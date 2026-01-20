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

namespace Tests\Sylius\Bundle\ApiBundle\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\EventSubscriber\ProductDeletionEventSubscriber;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ProductDeletionEventSubscriberTest extends TestCase
{
    private MockObject&ProductInPromotionRuleCheckerInterface $productInPromotionRuleChecker;

    private ProductDeletionEventSubscriber $productDeletionEventSubscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productInPromotionRuleChecker = $this->createMock(ProductInPromotionRuleCheckerInterface::class);
        $this->productDeletionEventSubscriber = new ProductDeletionEventSubscriber($this->productInPromotionRuleChecker);
    }

    public function testDoesNotThrowExceptionWhenProductIsNotBeingDeleted(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $event = new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $productMock,
        );

        $this->productInPromotionRuleChecker->expects(self::never())->method('isInUse')->with($productMock);

        /** should not throw exception */
        $this->productDeletionEventSubscriber->protectFromRemovingProductInUseByPromotionRule($event);
    }

    public function testDoesNotThrowExceptionWhenProductIsNotInUseByAPromotionRule(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $requestMock->expects(self::once())
            ->method('getMethod')
            ->willReturn(Request::METHOD_DELETE);

        $event = new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $productMock,
        );

        $this->productInPromotionRuleChecker
            ->expects(self::once())
            ->method('isInUse')
            ->with($productMock)
            ->willReturn(false);

        /** should not throw exception */
        $this->productDeletionEventSubscriber->protectFromRemovingProductInUseByPromotionRule($event);
    }

    public function testThrowsAnExceptionWhenTryingToDeleteProductThatIsInUseByAPromotionRule(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_DELETE);

        $event = new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $productMock,
        );

        $this->productInPromotionRuleChecker->expects(self::once())
            ->method('isInUse')
            ->with($productMock)
            ->willReturn(true);

        self::expectException(ResourceDeleteException::class);

        $this->productDeletionEventSubscriber->protectFromRemovingProductInUseByPromotionRule($event);
    }
}
