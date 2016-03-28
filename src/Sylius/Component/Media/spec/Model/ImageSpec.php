<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Media\Model;

use PhpSpec\ObjectBehavior;
use Symfony\Cmf\Bundle\MediaBundle\ImageInterface as CmfImageInterface;

/**
 * @author Aram Alipoor <aram.alipoor@gmail.com>
 */
class ImageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Media\Model\Image');
    }

    function it_should_implement_Sylius_image_interface()
    {
        $this->shouldImplement('Sylius\Component\Media\Model\ImageInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_not_have_media_by_default()
    {
        $this->getMedia()->shouldReturn(null);
    }

    function its_media_should_be_mutable(CmfImageInterface $media)
    {
        $this->setMedia($media);
        $this->getMedia()->shouldReturn($media);
    }

    function it_should_not_have_media_id_by_default()
    {
        $this->getMediaId()->shouldReturn(null);
    }

    function its_media_id_should_be_mutable()
    {
        $this->setMediaId('/cms/images/sample.png');
        $this->getMediaId()->shouldReturn('/cms/images/sample.png');
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }
}
