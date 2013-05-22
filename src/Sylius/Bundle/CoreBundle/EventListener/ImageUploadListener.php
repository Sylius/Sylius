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

use Sylius\Bundle\CoreBundle\Uploader\ImageUploaderInterface;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonInterface;
use Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\AssortmentBundle\Model\CustomizableProductInterface;
use Sylius\Bundle\AssortmentBundle\Model\Variant\VariantInterface;

class ImageUploadListener
{
    protected $uploaderProduct;
    protected $uploaderTaxon;

    public function __construct(ImageUploaderInterface $uploaderProduct, ImageUploaderInterface $uploaderTaxon)
    {
        $this->uploaderProduct = $uploaderProduct;
        $this->uploaderTaxon = $uploaderTaxon;
    }

    public function uploadProductImage(GenericEvent $event)
    {
        $subject = $event->getSubject();
        if (!$subject instanceof CustomizableProductInterface && !$subject instanceof VariantInterface){
            throw new \InvalidArgumentException('CustomizableProductInterface or VariantInterface expected.');
        }

        $variant = $subject instanceof VariantInterface ? $subject : $subject->getMasterVariant();

        foreach ($variant->getImages() as $image) {
            if (null === $image->getId()) {
                $this->uploaderProduct->upload($image);
            }
        }
    }

    public function uploadTaxonImage(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if (!$subject instanceof TaxonInterface){
            throw new \InvalidArgumentException('TaxonInterface expected.');
        }

        if ($subject->hasImageFile()) {
            $this->uploaderTaxon->upload($subject);
        }

    }

    public function uploadTaxonomyImage(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if (!$subject instanceof TaxonomyInterface){
            throw new \InvalidArgumentException('TaxonomyInterface expected.');
        }

        if ($subject->getRoot()->hasImageFile()) {
            $this->uploaderTaxon->upload($subject->getRoot());
        }

    }
}
