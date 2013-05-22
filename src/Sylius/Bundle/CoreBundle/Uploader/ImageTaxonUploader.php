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
use Gaufrette\Filesystem;

class ImageTaxonUploader implements ImageUploaderInterface
{
    protected $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function upload(ImageInterface $image)
    {
        if (!$image->hasImageFile()) {
            throw new \InvalidArgumentException('The given image has no file attached.');
        }

        if (null !== $image->getImagePath()) {
            $this->remove($image->getImagePath());
        }

        do {
            $hash = md5(uniqid(mt_rand(), true));
            $path = $this->expandPath($hash.'.'.$image->getImageFile()->guessExtension());
        } while ($this->filesystem->has($path));

        $image->setImagePath($path);

        $this->filesystem->write(
            $image->getImagePath(),
            file_get_contents($image->getImageFile()->getPathname())
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
