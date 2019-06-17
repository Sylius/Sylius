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

namespace Sylius\Behat\Service\Uploader;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploader as BaseImageUploader;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Webmozart\Assert\Assert;

final class ImageUploader extends BaseImageUploader implements ImageUploaderInterface
{
    public function upload(ImageInterface $image): void
    {
        if (!$image->hasFile()) {
            return;
        }

        $file = $image->getFile();

        /** @var UploadedFile $file */
        Assert::isInstanceOf($file, File::class);

        if (null !== $image->getPath() && $this->has($image->getPath())) {
            $this->remove($image->getPath());
        }

        do {
            $hash = bin2hex(random_bytes(16));
            $path = $this->expandPath($hash . '/' . $file->getClientOriginalName());
        } while ($this->isAdBlockingProne($path) || $this->filesystem->has($path));

        $image->setPath($path);

        $this->filesystem->write(
            $image->getPath(),
            file_get_contents($image->getFile()->getPathname())
        );
    }

    private function expandPath(string $path): string
    {
        return sprintf(
            '%s/%s/%s',
            substr($path, 0, 2),
            substr($path, 2, 2),
            substr($path, 4)
        );
    }

    private function has(string $path): bool
    {
        return $this->filesystem->has($path);
    }

    private function isAdBlockingProne(string $path): bool
    {
        return strpos($path, 'ad') !== false;
    }
}
