<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Uploader;

use Sylius\Bundle\CoreBundle\Model\ImageInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class ImageUploader implements ImageUploaderInterface
{
    protected $filesystem;
    protected $uploadDir;

    /**
     * @param Filesystem $filesystem
     * @param string     $uploadDir
     */
    public function __construct(Filesystem $filesystem, $uploadDir)
    {
        if (!$filesystem->exists($uploadDir)) {
            $filesystem->mkdir($uploadDir);
        }

        $this->filesystem = $filesystem;
        $this->uploadDir  = $uploadDir;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(ImageInterface $image)
    {
        if (!$image->hasFile()) {
            return;
        }

        if (null !== $image->getPath()) {
            $this->remove($image->getPath());
        }

        do {
            $hash = md5(uniqid(mt_rand(), true));
            $path = $this->transformPath($this->expandPath($hash.'.'.$image->getFile()->guessExtension()));
        } while ($this->filesystem->exists($path));

        $image->setPath($path);

        $this->filesystem->dumpFile(
            $image->getPath(),
            file_get_contents($image->getFile()->getPathname())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function remove($path)
    {
        try {
            $this->filesystem->remove($this->transformPath($path));
        } catch (IOException $e) {
            return false;
        }

        return true;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function expandPath($path)
    {
        return sprintf(
            '%s/%s/%s',
            substr($path, 0, 2),
            substr($path, 2, 2),
            substr($path, 4)
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function transformPath($path)
    {
        return sprintf('%s/%s', $this->uploadDir, $path);
    }
}
