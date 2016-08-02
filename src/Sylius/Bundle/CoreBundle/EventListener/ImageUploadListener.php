<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Uploader\ImageUploaderInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Variation\Resolver\VariantResolverInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

class ImageUploadListener
{
    /**
     * @var ImageUploaderInterface
     */
    protected $uploader;

    /**
     * @var VariantResolverInterface
     */
    protected $variantResolver;

    /**
     * @param ImageUploaderInterface $uploader
     * @param VariantResolverInterface $variantResolver
     */
    public function __construct(ImageUploaderInterface $uploader, VariantResolverInterface $variantResolver)
    {
        $this->uploader = $uploader;
        $this->variantResolver = $variantResolver;
    }

    /**
     * @param GenericEvent $event
     */
    public function uploadProductVariantImage(GenericEvent $event)
    {
        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, ProductVariantInterface::class);

        $this->uploadProductVariantImages($subject);
    }

    /**
     * @param GenericEvent $event
     */
    public function uploadProductImage(GenericEvent $event)
    {
        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, ProductInterface::class);

        if ($subject->isSimple()) {
            $variant = $this->variantResolver->getVariant($subject);
            $this->uploadProductVariantImages($variant);
        }
    }

    /**
     * @param GenericEvent $event
     */
    public function uploadTaxonImage(GenericEvent $event)
    {
        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, TaxonInterface::class);

        if ($subject->hasFile()) {
            $this->uploader->upload($subject);
        }
    }

    /**
     * @param ProductVariantInterface $productVariant
     */
    private function uploadProductVariantImages(ProductVariantInterface $productVariant)
    {
        $images = $productVariant->getImages();
        foreach ($images as $image) {
            if ($image->hasFile()) {
                $this->uploader->upload($image);
            }

            // Upload failed? Let's remove that image.
            if (null === $image->getPath()) {
                $images->removeElement($image);
            }
        }
    }
}
