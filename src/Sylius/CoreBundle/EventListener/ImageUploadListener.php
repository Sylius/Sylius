<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\CoreBundle\EventListener;

use Sylius\Core\Model\ProductInterface;
use Sylius\Core\Model\ProductVariantInterface;
use Sylius\Core\Uploader\ImageUploaderInterface;
use Sylius\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

class ImageUploadListener
{
    /**
     * @var ImageUploaderInterface
     */
    protected $uploader;

    /**
     * @param ImageUploaderInterface $uploader
     */
    public function __construct(ImageUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
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
            $this->uploadProductVariantImages($subject->getFirstVariant());
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
