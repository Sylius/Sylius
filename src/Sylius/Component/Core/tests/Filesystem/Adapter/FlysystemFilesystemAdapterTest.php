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

namespace Tests\Sylius\Component\Core\Filesystem\Adapter;

use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Filesystem\Adapter\FilesystemAdapterInterface;
use Sylius\Component\Core\Filesystem\Adapter\FlysystemFilesystemAdapter;
use Sylius\Component\Core\Filesystem\Exception\FileNotFoundException;

final class FlysystemFilesystemAdapterTest extends TestCase
{
    private FilesystemOperator&MockObject $filesystem;

    private FlysystemFilesystemAdapter $adapter;

    protected function setUp(): void
    {
        $this->filesystem = $this->createMock(FilesystemOperator::class);
        $this->adapter = new FlysystemFilesystemAdapter($this->filesystem);
    }

    public function testShouldImplementFilesystemAdapterInterface(): void
    {
        $this->assertInstanceOf(FilesystemAdapterInterface::class, $this->adapter);
    }

    public function testShouldReturnTrueOfTheFileExist(): void
    {
        $this->filesystem->expects($this->once())->method('fileExists')->with('/path/to/some-file')->willReturn(true);

        $this->assertTrue($this->adapter->has('/path/to/some-file'));
    }

    public function testShouldReturnFalseIfTheFileExist(): void
    {
        $this->filesystem->expects($this->once())->method('fileExists')->with('/path/to/some-file')->willReturn(false);

        $this->assertFalse($this->adapter->has('/path/to/some-file'));
    }

    public function testShouldWriteTheGivenContentIntoTheFile(): void
    {
        $this->filesystem->expects($this->once())->method('write')->with('/path/to/some-file', 'content');

        $this->adapter->write('/path/to/some-file', 'content');
    }

    public function testShouldDeleteGivenFile(): void
    {
        $this->filesystem->expects($this->once())->method('fileExists')->with('/path/to/some-file')->willReturn(true);
        $this->filesystem->expects($this->once())->method('delete')->with('/path/to/some-file');

        $this->adapter->delete('/path/to/some-file');
    }

    public function testShouldThrowExceptionIfFileDoesNotExistWhileDeleting(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->filesystem->expects($this->once())->method('fileExists')->with('/path/to/some-file')->willReturn(false);
        $this->filesystem->expects($this->never())->method('delete')->with('/path/to/some-file');

        $this->adapter->delete('/path/to/some-file');
    }
}
