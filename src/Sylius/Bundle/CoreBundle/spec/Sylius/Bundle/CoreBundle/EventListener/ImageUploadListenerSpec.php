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

class ImageUploadListenerSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CoreBundle\Uploader\ImageUploaderInterface $uploader
     */
    function let($uploader)
    {
        $this->beConstructedWith($uploader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\ImageUploadListener');
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent  $event
     * @param Sylius\Bundle\CoreBundle\Model\Variant          $variant
     * @param Sylius\Bundle\CoreBundle\Model\ProductInterface $product
     * @param Sylius\Bundle\CoreBundle\Model\ImageInterface   $image
     */
    function it_uses_image_uploader_to_upload_images($event, $variant, $product, $image, $uploader)
    {
        $event->getSubject()->willReturn($product);
        $product->getMasterVariant()->willReturn($variant);
        $variant->getImages()->willReturn(array($image));
        $uploader->upload($image)->shouldBeCalled();

        $this->uploadProductImage($event);
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     * @param Sylius\Bundle\CoreBundle\Model\Taxon           $taxon
     * @param Sylius\Bundle\CoreBundle\Model\ImageInterface  $image
     */
    function it_uses_image_uploader_to_upload_taxon_image($event, $taxon, $image, $uploader)
    {
        $event->getSubject()->willReturn($taxon);
        $uploader->upload($taxon)->shouldBeCalled();
        $taxon->hasFile()->willReturn(true);
        $this->uploadTaxonImage($event);
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     */
    function it_throws_exception_if_event_subject_is_not_a_product($event, $uploader)
    {
        $event->getSubject()->willReturn($uploader);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringUploadProductImage($event)
        ;
    }
}
