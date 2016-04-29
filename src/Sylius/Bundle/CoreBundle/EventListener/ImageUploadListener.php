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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

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
    public function uploadProductImage(GenericEvent $event)
    {
        $subject = $event->getSubject();
        if (!$subject instanceof ProductInterface && !$subject instanceof ProductVariantInterface) {
            throw new UnexpectedTypeException(
                $subject,
                'Sylius\Component\Core\Model\ProductInterface or Sylius\Component\Core\Model\ProductVariantInterface'
            );
        }

        $variant = $subject instanceof ProductVariantInterface ? $subject : $subject->getMasterVariant();

        $images = $variant->getImages();
        foreach ($images as $image) {
            $this->uploader->upload($image);

            // Upload failed? Let's remove that image.
            if (null === $image->getPath()) {
                $images->removeElement($image);
            }
        }
    }

    /**
     * @param GenericEvent $event
     */
    public function uploadTaxonImage(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if (!$subject instanceof TaxonInterface) {
            throw new UnexpectedTypeException(
                $subject,
                TaxonInterface::class
            );
        }

        if ($subject->hasFile()) {
            $this->uploader->upload($subject);
        }
    }
}
