<?php

namespace spec\Sylius\Component\Core\Filesystem\Adapter;

use Gaufrette\FilesystemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Filesystem\Adapter\FilesystemAdapterInterface;

class GaufretteFilesystemAdapterSpec extends ObjectBehavior
{
    function let(FilesystemInterface $filesystem): void
    {
        $this->beConstructedWith($filesystem);
    }

    function it_is_a_filesystem_adapter(): void
    {
        $this->shouldImplement(FilesystemAdapterInterface::class);
    }

    function it_returns_true_if_the_file_exists(FilesystemInterface $filesystem): void
    {
        $filesystem->has('/path/to/some-file')->willReturn(true);

        $this->has('/path/to/some-file')->shouldReturn(true);
    }

    function it_returns_false_if_the_file_exists(FilesystemInterface $filesystem): void
    {
        $filesystem->has('/path/to/some-file')->willReturn(false);

        $this->has('/path/to/some-file')->shouldReturn(false);
    }

    function it_writes_the_given_content_into_the_file(FilesystemInterface $filesystem): void
    {
        $filesystem->write('/path/to/some-file', 'content')->shouldBeCalled();

        $this->write('/path/to/some-file', 'content');
    }

    function it_deletes_the_given_file(FilesystemInterface $filesystem): void
    {
        $filesystem->has('/path/to/some-file')->willReturn(true);
        $filesystem->delete('/path/to/some-file')->willReturn(true)->shouldBeCalled();

        $this->delete('/path/to/some-file');
    }

    function it_throws_exception_if_file_does_not_exist_while_deleting(FilesystemInterface $filesystem): void
    {
        $filesystem->has('/path/to/some-file')->willReturn(false);
        $filesystem->delete('/path/to/some-file')->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('delete', ['/path/to/some-file'])
        ;
    }
}
