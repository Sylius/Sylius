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

namespace Sylius\Component\Core\Uploader;

use Gaufrette\Filesystem;
use Sylius\Component\Core\Model\FileInterface;
use Symfony\Component\HttpFoundation\File\File;
use Webmozart\Assert\Assert;

class FileUploader implements FileUploaderInterface
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(FileInterface $file): void
    {
        if (!$file->hasFile()) {
            return;
        }

        $uploadedFile = $file->getFile();

        /** @var File $file */
        Assert::isInstanceOf($uploadedFile, File::class);

        if (null !== $file->getPath() && $this->has($file->getPath())) {
            $this->remove($file->getPath());
        }

        do {
            $hash = bin2hex(random_bytes(16));
            $path = $this->expandPath($hash . '.' . $uploadedFile->guessExtension());
        } while ($this->filesystem->has($path));

        $file->setPath($path);

        $this->filesystem->write(
            $file->getPath(),
            file_get_contents($file->getFile()->getPathname())
        );
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $path): bool
    {
        if ($this->filesystem->has($path)) {
            return $this->filesystem->delete($path);
        }

        return false;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function expandPath(string $path): string
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
     * @return bool
     */
    private function has(string $path): bool
    {
        return $this->filesystem->has($path);
    }
}
