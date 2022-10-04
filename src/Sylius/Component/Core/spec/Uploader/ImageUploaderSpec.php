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

use Gaufrette\FilesystemInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Filesystem\Adapter\FilesystemAdapterInterface;
use Sylius\Component\Core\Filesystem\Exception\FileNotFoundException;
use Sylius\Component\Core\Generator\ImagePathGeneratorInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\File;

final class ImageUploaderSpec extends ObjectBehavior
{
    function let(ImageInterface $image): void
    {
        $file = new File(__FILE__);
        $image->getFile()->willReturn($file);
    }

    function it_is_an_image_uploader(
        FilesystemAdapterInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
    ): void {
        $this->beConstructedWith($filesystem, $imagePathGenerator);

        $this->shouldImplement(ImageUploaderInterface::class);
    }

    function it_triggers_a_deprecation_exception_if_no_image_path_generator_is_passed(
        FilesystemAdapterInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
    ): void {
        $this->beConstructedWith($filesystem, $imagePathGenerator);

        $this
            ->shouldTrigger(\E_USER_DEPRECATED)
            ->during('__construct', [$filesystem])
        ;
    }

    function it_triggers_a_deprecation_exception_if_gaufrette_filesystem_is_passed(
        FilesystemInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
    ): void {
        $this->beConstructedWith($filesystem, $imagePathGenerator);

        $this
            ->shouldTrigger(\E_USER_DEPRECATED)
            ->duringInstantiation()
        ;
    }

    function it_uploads_an_image(
        FilesystemAdapterInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
        ImageInterface $image,
    ): void {
        $this->beConstructedWith($filesystem, $imagePathGenerator);

        $image->hasFile()->willReturn(true);
        $image->getPath()->willReturn('foo.jpg');

        $filesystem->has(Argument::any())->willReturn(false);

        $filesystem->delete(Argument::any())->shouldNotBeCalled();

        $imagePathGenerator->generate($image)->willReturn('image/path/image.jpg');

        $image->setPath('image/path/image.jpg')->will(function ($args) use ($image, $filesystem) {
            $image->getPath()->willReturn($args[0]);

            $filesystem->write($args[0], Argument::any())->shouldBeCalled();
        })->shouldBeCalled();

        $this->upload($image);
    }

    function it_replaces_an_image(
        FilesystemAdapterInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
        ImageInterface $image,
    ): void {
        $this->beConstructedWith($filesystem, $imagePathGenerator);

        $image->hasFile()->willReturn(true);
        $image->getPath()->willReturn('foo.jpg');

        $filesystem->has(Argument::any())->willReturn(false);
        $filesystem->has('foo.jpg')->willReturn(true);

        $filesystem->delete('foo.jpg')->shouldBeCalled();

        $imagePathGenerator->generate($image)->willReturn('image/path/image.jpg');

        $image->setPath('image/path/image.jpg')->will(function ($args) use ($image, $filesystem) {
            $image->getPath()->willReturn($args[0]);

            $filesystem->write($args[0], Argument::any())->shouldBeCalled();
        })->shouldBeCalled();

        $this->upload($image);
    }

    function it_removes_an_image_if_one_exists(
        FilesystemAdapterInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
    ): void {
        $this->beConstructedWith($filesystem, $imagePathGenerator);

        $filesystem->delete('path/to/img')->shouldBeCalled();

        $this->remove('path/to/img')->shouldReturn(true);
    }

    function it_does_not_remove_an_image_if_one_does_not_exist(
        FilesystemAdapterInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
    ): void {
        $this->beConstructedWith($filesystem, $imagePathGenerator);

        $filesystem->delete('path/to/img')->willThrow(FileNotFoundException::class)->shouldBeCalled();

        $this->remove('path/to/img')->shouldReturn(false);
    }
}
