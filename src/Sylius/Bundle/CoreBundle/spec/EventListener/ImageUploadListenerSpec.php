<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ImageUploadListenerSpec extends ObjectBehavior
{
    function let(ImageUploaderInterface $uploader)
    {
        $this->beConstructedWith($uploader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\ImageUploadListener');
    }

    function it_uses_image_uploader_to_upload_images(
        GenericEvent $event,
        ProductVariantInterface $variant,
        ImageInterface $image,
        $uploader
    ) {
        $event->getSubject()->willReturn($variant);
        $variant->getImages()->willReturn([$image]);
        $uploader->upload($image)->shouldBeCalled();
        $image->getPath()->willReturn('some_path');

        $this->uploadProductImage($event);
    }

    function it_uses_image_uploader_to_upload_taxon_image(
        GenericEvent $event,
        Taxon $taxon,
        $uploader
    ) {
        $event->getSubject()->willReturn($taxon);
        $uploader->upload($taxon)->shouldBeCalled();
        $taxon->hasFile()->willReturn(true);

        $this->uploadTaxonImage($event);
    }

    function it_throws_exception_if_event_subject_is_not_a_product_variant(
        GenericEvent $event,
        $uploader
    ) {
        $event->getSubject()->willReturn($uploader);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringUploadProductImage($event)
        ;
    }
}
