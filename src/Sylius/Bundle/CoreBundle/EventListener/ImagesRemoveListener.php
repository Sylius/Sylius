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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;

/**
 * @internal
 */
final class ImagesRemoveListener
{
    /** @var string[] */
    private array $imagesToDelete = [];

    public function __construct(
        private ImageUploaderInterface $imageUploader,
        private CacheManager $cacheManager,
        private FilterManager $filterManager,
    ) {
    }

    public function onFlush(OnFlushEventArgs $event): void
    {
        foreach ($event->getObjectManager()->getUnitOfWork()->getScheduledEntityDeletions() as $entityDeletion) {
            if (!$entityDeletion instanceof ImageInterface) {
                continue;
            }

            $path = $entityDeletion->getPath();

            if (null === $path) {
                continue;
            }

            if (!in_array($path, $this->imagesToDelete, true)) {
                $this->imagesToDelete[] = $path;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $event): void
    {
        foreach ($this->imagesToDelete as $key => $imagePath) {
            $this->imageUploader->remove($imagePath);
            $this->cacheManager->remove($imagePath, array_keys($this->filterManager->getFilterConfiguration()->all()));
            unset($this->imagesToDelete[$key]);
        }
    }
}
