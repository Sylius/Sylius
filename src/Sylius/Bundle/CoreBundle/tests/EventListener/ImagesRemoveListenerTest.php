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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\ImagesRemoveListener;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;

final class ImagesRemoveListenerTest extends TestCase
{
    private ImageUploaderInterface&MockObject $imageUploader;

    private CacheManager&MockObject $cacheManager;

    private FilterManager&MockObject $filterManager;

    private ImagesRemoveListener $listener;

    private MockObject&OnFlushEventArgs $onFlushEvent;

    private MockObject&PostFlushEventArgs $postFlushEvent;

    private EntityManagerInterface&MockObject $entityManager;

    private MockObject&UnitOfWork $unitOfWork;

    private ImageInterface&MockObject $image;

    private MockObject&ProductInterface $product;

    private FilterConfiguration&MockObject $filterConfiguration;

    protected function setUp(): void
    {
        $this->imageUploader = $this->createMock(ImageUploaderInterface::class);
        $this->cacheManager = $this->createMock(CacheManager::class);
        $this->filterManager = $this->createMock(FilterManager::class);
        $this->listener = new ImagesRemoveListener(
            $this->imageUploader,
            $this->cacheManager,
            $this->filterManager,
        );
        $this->onFlushEvent = $this->createMock(OnFlushEventArgs::class);
        $this->postFlushEvent = $this->createMock(PostFlushEventArgs::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->unitOfWork = $this->createMock(UnitOfWork::class);
        $this->image = $this->createMock(ImageInterface::class);
        $this->product = $this->createMock(ProductInterface::class);
        $this->filterConfiguration = $this->createMock(FilterConfiguration::class);
    }

    public function testItIsInitializable(): void
    {
        $this->assertInstanceOf(ImagesRemoveListener::class, $this->listener);
    }

    public function testItSavesScheduledEntityDeletionsImagesPaths(): void
    {
        $this->onFlushEvent->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);
        $this->unitOfWork->method('getScheduledEntityDeletions')->willReturn([$this->image, $this->product]);

        $this->image->expects($this->once())->method('getPath');

        $this->listener->onFlush($this->onFlushEvent);
    }

    public function testItRemovesSavedImagesPaths(): void
    {
        $this->onFlushEvent->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);
        $this->unitOfWork->method('getScheduledEntityDeletions')->willReturn([$this->image, $this->product]);
        $this->image->method('getPath')->willReturn('image/path');

        $this->listener->onFlush($this->onFlushEvent);

        $this->imageUploader->expects($this->once())->method('remove')->with('image/path');

        $this->filterManager->method('getFilterConfiguration')->willReturn($this->filterConfiguration);
        $this->filterConfiguration->method('all')->willReturn(['test' => 'Test']);

        $this->cacheManager->expects($this->once())->method('remove')->with('image/path', ['test']);

        $this->listener->postFlush($this->postFlushEvent);
    }

    public function testItRemovesSavedImagesPathsFromBothFilesystemAndServiceProperty(): void
    {
        $this->onFlushEvent->method('getObjectManager')->willReturn($this->entityManager);
        $this->entityManager->method('getUnitOfWork')->willReturn($this->unitOfWork);
        $this->unitOfWork->method('getScheduledEntityDeletions')->willReturn([$this->image, $this->product]);
        $this->image->method('getPath')->willReturn('image/path');

        $this->listener->onFlush($this->onFlushEvent);

        $this->imageUploader->expects($this->once())->method('remove')->with('image/path');

        $this->filterManager->method('getFilterConfiguration')->willReturn($this->filterConfiguration);
        $this->filterConfiguration->method('all')->willReturn(['test' => 'Test']);

        $this->cacheManager->expects($this->once())->method('remove')->with('image/path', ['test']);

        $this->listener->postFlush($this->postFlushEvent);
        $this->listener->postFlush($this->postFlushEvent);
    }
}
