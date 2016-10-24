<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\ImageAwareInterface;
use Sylius\Component\Core\Model\TaxonImage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class TaxonImageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TaxonImage::class);
    }

    function it_extends_an_image()
    {
        $this->shouldHaveType(Image::class);
    }

    function it_does_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_have_file_by_default()
    {
        $this->hasFile()->shouldReturn(false);
        $this->getFile()->shouldReturn(null);
    }

    function its_file_is_mutable()
    {
        $file = new \SplFileInfo(__FILE__);
        $this->setFile($file);
        $this->getFile()->shouldReturn($file);
    }

    function its_path_is_mutable()
    {
        $this->setPath(__FILE__);
        $this->getPath()->shouldReturn(__FILE__);
    }

    function it_does_not_have_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('banner');
        $this->getCode()->shouldReturn('banner');
    }

    function it_does_not_have_owner_by_default()
    {
        $this->getOwner()->shouldReturn(null);
    }

    function its_owner_is_mutable(ImageAwareInterface $owner)
    {
        $this->setOwner($owner);
        $this->getOwner()->shouldReturn($owner);
    }
}
