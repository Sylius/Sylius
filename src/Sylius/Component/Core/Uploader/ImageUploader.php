<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Uploader;

use Gaufrette\Filesystem;
use Sylius\Component\Core\Model\ImageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ImageUploader implements ImageUploaderInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var PathProviderInterface
     */
    private $pathProvider;

    /**
     * @param Filesystem $filesystem
     * @param PathProviderInterface $pathProvider
     */
    public function __construct(Filesystem $filesystem, PathProviderInterface $pathProvider)
    {
        $this->filesystem = $filesystem;
        $this->pathProvider = $pathProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(ImageInterface $image)
    {
        if (false === $image->hasFile()) {
            return;
        }

        $newFilePath = $this->pathProvider->provide($this->filesystem, $image->getFile());

        $openedFile = $image->getFile()->openFile();
        $this->filesystem->write($newFilePath, $openedFile->fread($openedFile->getSize()));

        $this->deleteOverwrittenImageIfExists($image);
        $image->setPath($newFilePath);
    }

    /**
     * @param ImageInterface $image
     */
    private function deleteOverwrittenImageIfExists(ImageInterface $image)
    {
        $oldFilePath = $image->getPath();
        if (null !== $oldFilePath) {
            $this->filesystem->delete($oldFilePath);
        }
    }
}
