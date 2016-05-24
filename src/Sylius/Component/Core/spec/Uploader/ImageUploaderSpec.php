<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Uploader;

use Gaufrette\Filesystem;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Uploader\ImageUploader;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Core\Uploader\PathGeneratorInterface;
use Sylius\Component\Core\Uploader\PathProviderInterface;

/**
 * @mixin ImageUploader
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ImageUploaderSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem, PathProviderInterface $pathProvider)
    {
        $this->beConstructedWith($filesystem, $pathProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImageUploader::class);
    }

    function it_is_Sylius_image_uploader()
    {
        $this->shouldImplement(ImageUploaderInterface::class);
    }

    function it_does_not_upload_image_if_there_is_no_file_attached(ImageInterface $image)
    {
        $image->hasFile()->willReturn(false);
        $image->getFile()->shouldNotBeCalled();

        $this->upload($image);
    }

    function it_uploads_an_image(
        Filesystem $filesystem,
        PathProviderInterface $pathProvider,
        ImageInterface $image,
        \SplFileInfo $fileInfo,
        \SplFileObject $fileObject
    ) {
        $image->hasFile()->willReturn(true);
        $image->getFile()->willReturn($fileInfo);

        $fileInfo->openFile(Argument::cetera())->willReturn($fileObject);

        $pathProvider->provide($filesystem, $fileInfo)->willReturn('path/to/file');

        $fileObject->getSize()->willReturn(42);
        $fileObject->fread(42)->willReturn('File contents');

        $image->getPath()->willReturn(null);
        $filesystem->delete(Argument::any())->shouldNotBeCalled();

        $filesystem->write('path/to/file', 'File contents')->shouldBeCalled();
        $image->setPath('path/to/file')->shouldBeCalled();

        $this->upload($image);
    }

    function it_replaces_existing_images_while_uploading_a_new_one(
        Filesystem $filesystem,
        PathProviderInterface $pathProvider,
        ImageInterface $image,
        \SplFileInfo $fileInfo,
        \SplFileObject $fileObject
    ) {
        $image->hasFile()->willReturn(true);
        $image->getFile()->willReturn($fileInfo);

        $fileInfo->openFile(Argument::cetera())->willReturn($fileObject);

        $pathProvider->provide($filesystem, $fileInfo)->willReturn('path/to/file');

        $fileObject->getSize()->willReturn(42);
        $fileObject->fread(42)->willReturn('File contents');

        $image->getPath()->willReturn('path/to/existing-file');
        $filesystem->delete('path/to/existing-file')->shouldBeCalled();

        $filesystem->write('path/to/file', 'File contents')->shouldBeCalled();
        $image->setPath('path/to/file')->shouldBeCalled();

        $this->upload($image);
    }

    function it_generates_a_name_for_an_image_until_it_is_unique(
        Filesystem $filesystem,
        PathProviderInterface $pathProvider,
        ImageInterface $image,
        \SplFileInfo $fileInfo,
        \SplFileObject $fileObject
    ) {
        $image->hasFile()->willReturn(true);
        $image->getFile()->willReturn($fileInfo);

        $fileInfo->openFile(Argument::cetera())->willReturn($fileObject);

        $pathProvider->provide($filesystem, $fileInfo)->willReturn('path/to/file');

        $fileObject->getSize()->willReturn(42);
        $fileObject->fread(42)->willReturn('File contents');

        $image->getPath()->willReturn(null);
        $filesystem->delete(Argument::any())->shouldNotBeCalled();

        $filesystem->write('path/to/file', 'File contents')->shouldBeCalled();
        $image->setPath('path/to/file')->shouldBeCalled();

        $this->upload($image);
    }
}
