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

namespace Tests\Sylius\Component\Core\Uploader;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Filesystem\Adapter\FilesystemAdapterInterface;
use Sylius\Component\Core\Filesystem\Exception\FileNotFoundException;
use Sylius\Component\Core\Generator\ImagePathGeneratorInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploader;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\File;

final class ImageUploaderTest extends TestCase
{
    private ImageInterface&MockObject $image;

    private FilesystemAdapterInterface&MockObject $filesystem;

    private ImagePathGeneratorInterface&MockObject $imagePathGenerator;

    private ImageUploader $imageUploader;

    protected function setUp(): void
    {
        $this->image = $this->createMock(ImageInterface::class);
        $this->filesystem = $this->createMock(FilesystemAdapterInterface::class);
        $this->imagePathGenerator = $this->createMock(ImagePathGeneratorInterface::class);
        $this->imageUploader = new ImageUploader($this->filesystem, $this->imagePathGenerator);
    }

    public function testShouldImplementImageUploaderInterface(): void
    {
        $this->assertInstanceOf(ImageUploaderInterface::class, $this->imageUploader);
    }

    public function testShouldUploadImage(): void
    {
        $currentPath = 'foo.jpg';
        $this->image->method('getFile')->willReturn(new File(__FILE__));
        $this->image->method('hasFile')->willReturn(true);
        $this->image->method('getPath')
            ->willReturnCallback(function () use (&$currentPath) {
                return $currentPath;
            });
        $this->filesystem->method('has')->willReturn(false);
        $this->filesystem->expects($this->never())->method('delete');
        $this->imagePathGenerator->expects($this->once())->method('generate')->with($this->image)->willReturn('image/path/image.jpg');
        $this->filesystem->expects($this->once())->method('write')->with('image/path/image.jpg', $this->anything());
        $this->image->expects($this->once())
            ->method('setPath')
            ->with('image/path/image.jpg')
            ->willReturnCallback(function ($path) use (&$currentPath) {
                $currentPath = $path;

                return null;
            });

        $this->imageUploader->upload($this->image);
    }

    public function testShouldReplaceImage(): void
    {
        $currentPath = 'foo.jpg';
        $this->image->method('getFile')->willReturn(new File(__FILE__));
        $this->image->method('hasFile')->willReturn(true);
        $this->image->method('getPath')->willReturnCallback(function () use (&$currentPath) {
            return $currentPath;
        });
        $this->filesystem->method('has')->willReturnCallback(fn ($path) => $path === 'foo.jpg');
        $this->filesystem->expects($this->once())->method('delete')->with('foo.jpg');
        $this->imagePathGenerator->expects($this->once())
            ->method('generate')
            ->with($this->image)
            ->willReturn('image/path/image.jpg');
        $this->filesystem->expects($this->once())
            ->method('write')
            ->with('image/path/image.jpg', $this->anything());
        $this->image->expects($this->once())
            ->method('setPath')
            ->with('image/path/image.jpg')
            ->willReturnCallback(function ($path) use (&$currentPath) {
                $currentPath = $path;

                return null;
            });

        $this->imageUploader->upload($this->image);
    }

    public function testShouldRemoveImageIfOneExist(): void
    {
        $this->filesystem->expects($this->once())->method('delete')->with('path/to/img');

        $this->assertTrue($this->imageUploader->remove('path/to/img'));
    }

    public function testShouldNotRemoveImageIfOneDoesNotExist(): void
    {
        $this->filesystem
            ->expects($this->once())
            ->method('delete')
            ->with('path/to/img')
            ->willThrowException(new FileNotFoundException('path/to/img'));

        $this->assertFalse($this->imageUploader->remove('path/to/img'));
    }
}
