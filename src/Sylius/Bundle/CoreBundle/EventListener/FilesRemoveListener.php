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

use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Sylius\Component\Core\Model\FileInterface;
use Sylius\Component\Core\Uploader\FileUploaderInterface;

final class FilesRemoveListener
{
    /** @var FileUploaderInterface */
    private $fileUploader;

    /** @var CacheManager */
    private $cacheManager;

    /** @var FilterManager */
    private $filterManager;

    public function __construct(FileUploaderInterface $fileUploader, CacheManager $cacheManager, FilterManager $filterManager)
    {
        $this->fileUploader = $fileUploader;
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $file = $event->getEntity();

        if ($file instanceof FileInterface) {
            $this->fileUploader->remove($file->getPath());
            $this->cacheManager->remove($file->getPath(), array_keys($this->filterManager->getFilterConfiguration()->all()));
        }
    }
}
