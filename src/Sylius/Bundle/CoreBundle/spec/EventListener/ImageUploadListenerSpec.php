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
use Prophecy\Argument;
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
        ImageUploaderInterface $uploader
    ) {
        $event->getSubject()->willReturn($variant);
        $variant->getImages()->willReturn([$image]);
        $image->hasFile()->willReturn(true);
        $image->getPath()->willReturn('some_path');
        $uploader->upload($image)->shouldBeCalled();

        $this->uploadProductVariantImage($event);
    }

    function it_uses_image_uploader_to_upload_images_for_simple_product(
        GenericEvent $event,
        ProductInterface $product,
        ProductVariantInterface $variant,
        ImageInterface $image,
        ImageUploaderInterface $uploader
    ) {
        $event->getSubject()->willReturn($product);
        $product->isSimple()->willReturn(true);
        $product->getFirstVariant()->willReturn($variant);
        $variant->getImages()->willReturn([$image]);
        $image->hasFile()->willReturn(true);
        $image->getPath()->willReturn('some_path');
        $uploader->upload($image)->shouldBeCalled();

        $this->uploadProductImage($event);
    }

    function it_does_nothing_when_product_is_not_simple(
        GenericEvent $event,
        ProductInterface $product,
        ImageUploaderInterface $uploader
    ) {
        $event->getSubject()->willReturn($product);
        $product->isSimple()->willReturn(false);
        $uploader->upload(Argument::any())->shouldNotBeCalled();

        $this->uploadProductImage($event);
    }

    function it_uses_image_uploader_to_upload_taxon_image(
        GenericEvent $event,
        Taxon $taxon,
        ImageUploaderInterface $uploader,
        ImageInterface $image
    ) {
        $event->getSubject()->willReturn($taxon);
        $taxon->hasImage()->willReturn(true);
        $taxon->getImage()->willReturn($image);
        $uploader->upload($taxon->getImage())->shouldBeCalled();
        $image->getPath()->willReturn('some_path');

        $this->uploadTaxonImage($event);
    }

    function it_throws_exception_if_event_subject_is_not_a_product_variant(
        GenericEvent $event,
        \stdClass $object
    ) {
        $event->getSubject()->willReturn($object);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringUploadProductVariantImage($event)
        ;
    }

    function it_throws_exception_if_event_subject_is_not_a_product(
        GenericEvent $event,
        \stdClass $object
    ) {
        $event->getSubject()->willReturn($object);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringUploadProductImage($event)
        ;
    }

    function it_throws_exception_if_event_subject_is_not_a_taxon(
        GenericEvent $event,
        \stdClass $object
    ) {
        $event->getSubject()->willReturn($object);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringUploadTaxonImage($event)
        ;
    }
}
