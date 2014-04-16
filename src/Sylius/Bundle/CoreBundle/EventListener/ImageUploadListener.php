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
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ImageUploadListener
{
    protected $uploader;

    public function __construct(ImageUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

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

        foreach ($variant->getImages() as $image) {
            $this->uploader->upload($image);
        }
    }

    public function uploadTaxonImage(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if (!$subject instanceof TaxonInterface) {
            throw new UnexpectedTypeException(
                $subject,
                'Sylius\Component\Taxonomy\Model\TaxonInterface'
            );
        }

        if ($subject->hasFile()) {
            $this->uploader->upload($subject);
        }
    }

    public function uploadTaxonomyImage(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if (!$subject instanceof TaxonomyInterface) {
            throw new UnexpectedTypeException(
                $subject,
                'Sylius\Component\Taxonomy\Model\TaxonomyInterface'
            );
        }

        if ($subject->getRoot()->hasFile()) {
            $this->uploader->upload($subject->getRoot());
        }
    }
}
