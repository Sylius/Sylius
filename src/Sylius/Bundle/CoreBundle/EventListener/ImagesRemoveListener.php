<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
    /** @var ImageUploaderInterface */
    private $imageUploader;

    /** @var CacheManager */
    private $cacheManager;

    /** @var FilterManager */
    private $filterManager;

    /** @var string[] */
    private $imagesToDelete = [];

    public function __construct(ImageUploaderInterface $imageUploader, CacheManager $cacheManager, FilterManager $filterManager)
    {
        $this->imageUploader = $imageUploader;
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;
    }

    public function onFlush(OnFlushEventArgs $event): void
    {
        foreach ($event->getEntityManager()->getUnitOfWork()->getScheduledEntityDeletions() as $entityDeletion) {
            if (!$entityDeletion instanceof ImageInterface) {
                continue;
            }

            if (!in_array($entityDeletion->getPath(), $this->imagesToDelete)) {
                $this->imagesToDelete[] = $entityDeletion->getPath();
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
