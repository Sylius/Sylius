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

class ImageUploader implements ImageUploaderInterface
{
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

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
            $path = $this->expandPath($hash.'.'.$image->getFile()->guessExtension());
        } while ($this->filesystem->has($path));

        $image->setPath($path);

        $this->filesystem->write(
            $image->getPath(),
            file_get_contents($image->getFile()->getPathname())
        );
    }

    public function remove($path)
    {
        return $this->filesystem->delete($path);
    }

    private function expandPath($path)
    {
        return sprintf(
            '%s/%s/%s',
            substr($path, 0, 2),
            substr($path, 2, 2),
            substr($path, 4)
        );
    }
}
