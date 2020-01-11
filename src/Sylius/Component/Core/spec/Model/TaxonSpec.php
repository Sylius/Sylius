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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ImagesAwareInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class TaxonSpec extends ObjectBehavior
{
    function it_is_a_taxon(): void
    {
        $this->shouldImplement(TaxonInterface::class);
    }

    function it_implements_an_image_aware_interface(): void
    {
        $this->shouldImplement(ImagesAwareInterface::class);
    }

    function it_initializes_an_image_collection_by_default(): void
    {
        $this->getImages()->shouldHaveType(Collection::class);
    }

    function it_adds_an_image(ImageInterface $image): void
    {
        $this->addImage($image);
        $this->hasImages()->shouldReturn(true);
        $this->hasImage($image)->shouldReturn(true);
    }

    function it_removes_an_image(ImageInterface $image): void
    {
        $this->addImage($image);
        $this->removeImage($image);
        $this->hasImage($image)->shouldReturn(false);
    }

    function it_returns_images_by_type(ImageInterface $image): void
    {
        $image->getType()->willReturn('thumbnail');
        $image->setOwner($this)->shouldBeCalled();

        $this->addImage($image);

        $this->getImagesByType('thumbnail')->shouldBeLike(new ArrayCollection([$image->getWrappedObject()]));
    }
}
