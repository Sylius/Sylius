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
use Sylius\Bundle\ApiBundle\EventSubscriber\CatalogPromotionEventSubscriber;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionAnnouncerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CatalogPromotionEventSubscriberTest extends TestCase
{
    private CatalogPromotionAnnouncerInterface&MockObject $catalogPromotionAnnouncerMock;

    private CatalogPromotionEventSubscriber $catalogPromotionEventSubscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->catalogPromotionAnnouncerMock = $this->createMock(CatalogPromotionAnnouncerInterface::class);
        $this->catalogPromotionEventSubscriber = new CatalogPromotionEventSubscriber($this->catalogPromotionAnnouncerMock);
    }

    public function testUsesAnnouncerToDispatchCatalogPromotionCreatedEventAfterWritingCatalogPromotion(): void
    {
        /** @var CatalogPromotionInterface|MockObject $catalogPromotionMock */
        $catalogPromotionMock = $this->createMock(CatalogPromotionInterface::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $this->catalogPromotionAnnouncerMock->expects(self::once())
            ->method('dispatchCatalogPromotionCreatedEvent')
            ->with($catalogPromotionMock);

        $this->catalogPromotionEventSubscriber->postWrite(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $catalogPromotionMock,
        ));
    }

    public function testUsesAnnouncerToDispatchCatalogPromotionUpdatedEventAfterChangingCatalogPromotion(): void
    {
        /** @var CatalogPromotionInterface|MockObject $catalogPromotionMock */
        $catalogPromotionMock = $this->createMock(CatalogPromotionInterface::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_PUT);

        $this->catalogPromotionAnnouncerMock->expects(self::once())
            ->method('dispatchCatalogPromotionUpdatedEvent')
            ->with($catalogPromotionMock);

        $this->catalogPromotionEventSubscriber->postWrite(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $catalogPromotionMock,
        ));
    }

    public function testDoesNothingAfterWritingOtherEntity(): void
    {
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $this->catalogPromotionAnnouncerMock->expects(self::never())->method('dispatchCatalogPromotionCreatedEvent');

        $this->catalogPromotionEventSubscriber->postWrite(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            new \stdClass(),
        ));
    }

    public function testDoesNothingIfThereIsAWrongRequestMethod(): void
    {
        /** @var CatalogPromotionInterface|MockObject $catalogPromotionMock */
        $catalogPromotionMock = $this->createMock(CatalogPromotionInterface::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_GET);

        $this->catalogPromotionAnnouncerMock->expects(self::never())
            ->method('dispatchCatalogPromotionCreatedEvent')
            ->with($catalogPromotionMock);

        $this->catalogPromotionEventSubscriber->postWrite(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $catalogPromotionMock,
        ));
    }
}
