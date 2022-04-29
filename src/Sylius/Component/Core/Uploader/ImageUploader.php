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

use enshrined\svgSanitize\Sanitizer;
use Gaufrette\FilesystemInterface;
use Sylius\Component\Core\Generator\ImagePathGeneratorInterface;
use Sylius\Component\Core\Generator\UploadedImagePathGenerator;
use Sylius\Component\Core\Model\ImageInterface;
use Symfony\Component\HttpFoundation\File\File;
use Webmozart\Assert\Assert;

class ImageUploader implements ImageUploaderInterface
{
    private const MIME_SVG_XML = 'image/svg+xml';

    private const MIME_SVG = 'image/svg';

    /** @var FilesystemInterface */
    protected $filesystem;

    /** @var ImagePathGeneratorInterface */
    protected $imagePathGenerator;

    /** @var Sanitizer */
    protected $sanitizer;

    public function __construct(
        FilesystemInterface $filesystem,
        ?ImagePathGeneratorInterface $imagePathGenerator = null
    ) {
        $this->filesystem = $filesystem;

        if ($imagePathGenerator === null) {
            @trigger_error(sprintf(
                'Not passing an $imagePathGenerator to %s constructor is deprecated since Sylius 1.6 and will be not possible in Sylius 2.0.',
                self::class
            ), \E_USER_DEPRECATED);
        }

        $this->imagePathGenerator = $imagePathGenerator ?? new UploadedImagePathGenerator();
        $this->sanitizer = new Sanitizer();
    }

    public function upload(ImageInterface $image): void
    {
        if (!$image->hasFile()) {
            return;
        }

        /** @var File $file */
        $file = $image->getFile();

        Assert::isInstanceOf($file, File::class);

        $fileContent = $this->sanitizeContent(file_get_contents($file->getPathname()), $file->getMimeType());

        if (null !== $image->getPath() && $this->has($image->getPath())) {
            $this->remove($image->getPath());
        }

        do {
            $path = $this->imagePathGenerator->generate($image);
        } while ($this->isAdBlockingProne($path) || $this->filesystem->has($path));

        $image->setPath($path);

        $this->filesystem->write($image->getPath(), $fileContent);
    }

    public function remove(string $path): bool
    {
        if ($this->filesystem->has($path)) {
            return $this->filesystem->delete($path);
        }

        return false;
    }

    protected function sanitizeContent(string $fileContent, string $mimeType): string
    {
        if (self::MIME_SVG_XML === $mimeType || self::MIME_SVG === $mimeType) {
            $fileContent = $this->sanitizer->sanitize($fileContent);
        }

        return $fileContent;
    }

    private function has(string $path): bool
    {
        return $this->filesystem->has($path);
    }

    /**
     * Will return true if the path is prone to be blocked by ad blockers
     */
    private function isAdBlockingProne(string $path): bool
    {
        return strpos($path, 'ad') !== false;
    }
}
