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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterConfiguration;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\ImagesRemoveListener;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;

final class ImagesRemoveListenerSpec extends ObjectBehavior
{
    function let(ImageUploaderInterface $imageUploader, CacheManager $cacheManager, FilterManager $filterManager): void
    {
        $this->beConstructedWith($imageUploader, $cacheManager, $filterManager);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(ImagesRemoveListener::class);
    }

    function it_saves_scheduled_entity_deletions_images_paths(
        OnFlushEventArgs $event,
        EntityManagerInterface $entityManager,
        UnitOfWork $unitOfWork,
        ImageInterface $image,
        ProductInterface $product,
    ): void {
        $event->getObjectManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityDeletions()->willReturn([$image, $product]);

        $image->getPath()->shouldBeCalled();

        $this->onFlush($event);
    }

    function it_removes_saved_images_paths(
        ImageUploaderInterface $imageUploader,
        CacheManager $cacheManager,
        FilterManager $filterManager,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        EntityManagerInterface $entityManager,
        UnitOfWork $unitOfWork,
        ImageInterface $image,
        ProductInterface $product,
        FilterConfiguration $filterConfiguration,
    ): void {
        $onFlushEvent->getObjectManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityDeletions()->willReturn([$image, $product]);

        $image->getPath()->willReturn('image/path');

        $this->onFlush($onFlushEvent);

        $imageUploader->remove('image/path')->shouldBeCalled();

        $filterManager->getFilterConfiguration()->willReturn($filterConfiguration);
        $filterConfiguration->all()->willReturn(['test' => 'Test']);

        $cacheManager->remove('image/path', ['test'])->shouldBeCalled();

        $this->postFlush($postFlushEvent);
    }

    function it_removes_saved_images_paths_from_both_filesystem_and_service_property(
        ImageUploaderInterface $imageUploader,
        CacheManager $cacheManager,
        FilterManager $filterManager,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        EntityManagerInterface $entityManager,
        UnitOfWork $unitOfWork,
        ImageInterface $image,
        ProductInterface $product,
        FilterConfiguration $filterConfiguration,
    ): void {
        $onFlushEvent->getObjectManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityDeletions()->willReturn([$image, $product]);

        $image->getPath()->willReturn('image/path');

        $this->onFlush($onFlushEvent);

        $imageUploader->remove('image/path')->shouldBeCalledOnce();

        $filterManager->getFilterConfiguration()->willReturn($filterConfiguration);
        $filterConfiguration->all()->willReturn(['test' => 'Test']);

        $cacheManager->remove('image/path', ['test'])->shouldBeCalledOnce();

        $this->postFlush($postFlushEvent);
        $this->postFlush($postFlushEvent);
    }
}
