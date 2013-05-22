<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Uploader;

use PHPSpec2\ObjectBehavior;
use Symfony\Component\HttpFoundation\File\File;

class ImageTaxonUploader extends ObjectBehavior
{
    /**
     * @param Gaufrette\Filesystem                                  $filesystem
     * @param Sylius\Bundle\CoreBundle\Model\ImageTaxonInterface    $image
     */
    function let($filesystem, $image)
    {
        $filesystem->has(ANY_ARGUMENT)->willReturn(false);

        $file = new \Symfony\Component\HttpFoundation\File\File(__FILE__, 'img.jpg');
        $image->getId()->willReturn(null);
        $image->getImageFile()->willReturn($file);

        $this->beConstructedWith($filesystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Uploader\ImageTaxonUploader');
    }

    function it_is_Sylius_image_uploader()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Uploader\ImageUploaderInterface');
    }

    function it_uploads_image($filesystem, $image)
    {
        $filesystem->write(ANY_ARGUMENT, ANY_ARGUMENT)->shouldBeCalled();

        $this->upload($image);
    }

    function it_throws_exception_if_there_is_no_file_attached($image)
    {
        $image->hasImageFile()->willReturn(false);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringUpload($image)
        ;
    }
}
