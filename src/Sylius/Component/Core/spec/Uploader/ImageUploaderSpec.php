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
use Sylius\Component\Core\Generator\ImagePathGeneratorInterface;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\File;

final class ImageUploaderSpec extends ObjectBehavior
{
    function let(
        FilesystemInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
        ImageInterface $image,
    ): void {
        $filesystem->has(Argument::any())->willReturn(false);

        $file = new File(__FILE__);
        $image->getFile()->willReturn($file);

        $this->beConstructedWith($filesystem, $imagePathGenerator);
    }

    function it_is_an_image_uploader(): void
    {
        $this->shouldImplement(ImageUploaderInterface::class);
    }

    function it_triggers_a_deprecation_exception_if_no_image_path_generator_is_passed(
        FilesystemInterface $filesystem,
        ImageInterface $image,
    ): void {
        $filesystem->has(Argument::any())->willReturn(false);

        $file = new File(__FILE__);
        $image->getFile()->willReturn($file);

        $this
            ->shouldTrigger(\E_USER_DEPRECATED)
            ->during('__construct', [$filesystem])
        ;
    }

    function it_uploads_an_image(
        FilesystemInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
        ImageInterface $image,
    ): void {
        $image->hasFile()->willReturn(true);
        $image->getPath()->willReturn('foo.jpg');

        $filesystem->has('foo.jpg')->willReturn(false);

        $filesystem->delete(Argument::any())->shouldNotBeCalled();

        $imagePathGenerator->generate($image)->willReturn('image/path/image.jpg');

        $image->setPath('image/path/image.jpg')->will(function ($args) use ($image, $filesystem) {
            $image->getPath()->willReturn($args[0]);

            $filesystem->write($args[0], Argument::any())->shouldBeCalled();
        })->shouldBeCalled();

        $this->upload($image);
    }

    function it_replaces_an_image(
        FilesystemInterface $filesystem,
        ImagePathGeneratorInterface $imagePathGenerator,
        ImageInterface $image,
    ): void {
        $image->hasFile()->willReturn(true);
        $image->getPath()->willReturn('foo.jpg');

        $filesystem->has('foo.jpg')->willReturn(true);

        $filesystem->delete('foo.jpg')->willReturn(true);

        $imagePathGenerator->generate($image)->willReturn('image/path/image.jpg');

        $image->setPath('image/path/image.jpg')->will(function ($args) use ($image, $filesystem) {
            $image->getPath()->willReturn($args[0]);

            $filesystem->write($args[0], Argument::any())->shouldBeCalled();
        })->shouldBeCalled();

        $this->upload($image);
    }

    function it_removes_an_image_if_one_exists(FilesystemInterface $filesystem): void
    {
        $filesystem->has('path/to/img')->willReturn(true);
        $filesystem->delete('path/to/img')->willReturn(true);

        $this->remove('path/to/img');
    }

    function it_does_not_remove_an_image_if_one_does_not_exist(FilesystemInterface $filesystem): void
    {
        $filesystem->has('path/to/img')->willReturn(false);
        $filesystem->delete('path/to/img')->shouldNotBeCalled();

        $this->remove('path/to/img');
    }
}
