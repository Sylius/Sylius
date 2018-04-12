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

namespace spec\Sylius\Component\Core\Uploader;

use Gaufrette\Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\FileInterface;
use Sylius\Component\Core\Uploader\FileUploaderInterface;
use Symfony\Component\HttpFoundation\File\File;

final class FileUploaderSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem, FileInterface $file): void
    {
        $filesystem->has(Argument::any())->willReturn(false);
        
        $uploadedFile = new File(__FILE__, 'img.jpg');
        $file->getFile()->willReturn($uploadedFile);

        $this->beConstructedWith($filesystem);
    }

    function it_is_a_file_uploader(): void
    {
        $this->shouldImplement(FileUploaderInterface::class);
    }

    function it_uploads_a_file(Filesystem $filesystem, FileInterface $file): void
    {
        $file->hasFile()->willReturn(true);
        $file->getPath()->willReturn('foo.jpg');

        $filesystem->has('foo.jpg')->willReturn(false);

        $filesystem->delete(Argument::any())->shouldNotBeCalled();

        $file->setPath(Argument::type('string'))->will(function ($args) use ($file, $filesystem) {
            $file->getPath()->willReturn($args[0]);

            $filesystem->write($args[0], Argument::any())->shouldBeCalled();
        })->shouldBeCalled();

        $this->upload($file);
    }

    function it_replaces_an_file(Filesystem $filesystem, FileInterface $file): void
    {
        $file->hasFile()->willReturn(true);
        $file->getPath()->willReturn('foo.jpg');

        $filesystem->has('foo.jpg')->willReturn(true);

        $filesystem->delete('foo.jpg')->willReturn(true);

        $file->setPath(Argument::type('string'))->will(function ($args) use ($file, $filesystem) {
            $file->getPath()->willReturn($args[0]);

            $filesystem->write($args[0], Argument::any())->shouldBeCalled();
        })->shouldBeCalled();

        $this->upload($file);
    }

    function it_removes_a_file_if_exists(Filesystem $filesystem): void
    {
        $filesystem->has('path/to/img')->willReturn(true);
        $filesystem->delete('path/to/img')->willReturn(true);

        $this->remove('path/to/img');
    }

    function it_does_not_remove_a_file_if_does_not_exist(FileSystem $filesystem): void
    {
        $filesystem->has('path/to/img')->willReturn(false);
        $filesystem->delete('path/to/img')->shouldNotBeCalled();

        $this->remove('path/to/img');
    }
}
