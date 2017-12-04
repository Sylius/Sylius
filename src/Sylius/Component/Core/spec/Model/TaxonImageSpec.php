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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Image;

final class TaxonImageSpec extends ObjectBehavior
{
    function it_extends_an_image(): void
    {
        $this->shouldHaveType(Image::class);
    }

    function it_does_not_have_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_does_not_have_file_by_default(): void
    {
        $this->hasFile()->shouldReturn(false);
        $this->getFile()->shouldReturn(null);
    }

    function its_file_is_mutable(): void
    {
        $file = new \SplFileInfo(__FILE__);
        $this->setFile($file);
        $this->getFile()->shouldReturn($file);
    }

    function its_path_is_mutable(): void
    {
        $this->setPath(__FILE__);
        $this->getPath()->shouldReturn(__FILE__);
    }

    function it_does_not_have_type_by_default(): void
    {
        $this->getType()->shouldReturn(null);
    }

    function its_type_is_mutable(): void
    {
        $this->setType('banner');
        $this->getType()->shouldReturn('banner');
    }

    function it_does_not_have_owner_by_default(): void
    {
        $this->getOwner()->shouldReturn(null);
    }

    function its_owner_is_mutable(): void
    {
        $owner = new \stdClass();

        $this->setOwner($owner);
        $this->getOwner()->shouldReturn($owner);
    }
}
