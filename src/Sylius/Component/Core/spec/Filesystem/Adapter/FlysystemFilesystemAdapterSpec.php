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

namespace spec\Sylius\Component\Core\Filesystem\Adapter;

use League\Flysystem\FilesystemOperator;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Filesystem\Adapter\FilesystemAdapterInterface;
use Sylius\Component\Core\Filesystem\Exception\FileNotFoundException;

final class FlysystemFilesystemAdapterSpec extends ObjectBehavior
{
    function let(FilesystemOperator $filesystem): void
    {
        $this->beConstructedWith($filesystem);
    }

    function it_is_a_filesystem_adapter(): void
    {
        $this->shouldImplement(FilesystemAdapterInterface::class);
    }

    function it_returns_true_if_the_file_exists(FilesystemOperator $filesystem): void
    {
        $filesystem->fileExists('/path/to/some-file')->willReturn(true);

        $this->has('/path/to/some-file')->shouldReturn(true);
    }

    function it_returns_false_if_the_file_exists(FilesystemOperator $filesystem): void
    {
        $filesystem->fileExists('/path/to/some-file')->willReturn(false);

        $this->has('/path/to/some-file')->shouldReturn(false);
    }

    function it_writes_the_given_content_into_the_file(FilesystemOperator $filesystem): void
    {
        $filesystem->write('/path/to/some-file', 'content')->shouldBeCalled();

        $this->write('/path/to/some-file', 'content');
    }

    function it_deletes_the_given_file(FilesystemOperator $filesystem): void
    {
        $filesystem->fileExists('/path/to/some-file')->willReturn(true);
        $filesystem->delete('/path/to/some-file')->shouldBeCalled();

        $this->delete('/path/to/some-file');
    }

    function it_throws_exception_if_file_does_not_exist_while_deleting(FilesystemOperator $filesystem): void
    {
        $filesystem->fileExists('/path/to/some-file')->willReturn(false);
        $filesystem->delete('/path/to/some-file')->shouldNotBeCalled();

        $this
            ->shouldThrow(FileNotFoundException::class)
            ->during('delete', ['/path/to/some-file'])
        ;
    }
}
