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
use Symfony\Component\HttpFoundation\File\File;

class ImageUploaderSpec extends ObjectBehavior
{
    function let(Filesystem $filesystem, ImageInterface $image)
    {
        $filesystem->has(Argument::any())->willReturn(false);

        $file = new File(__FILE__, 'img.jpg');
        $image->getFile()->willReturn($file);

        $this->beConstructedWith($filesystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Uploader\ImageUploader');
    }

    function it_is_Sylius_image_uploader()
    {
        $this->shouldImplement('Sylius\Component\Core\Uploader\ImageUploaderInterface');
    }

    function it_uploads_image($filesystem, $image)
    {
        $image->hasFile()->willReturn(true);
        $image->getPath()->willReturn('foo.jpg');

        $filesystem->delete(Argument::any())->shouldBeCalled();
        $filesystem->write(Argument::any(), Argument::any())->shouldBeCalled();

        $image->setPath(Argument::any())->shouldBeCalled();

        $this->upload($image);
    }
}
