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
    protected $uploader;

    public function __construct(ImageUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public function upload(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if (!$subject instanceof CustomizableProductInterface && !$subject instanceof VariantInterface && !$subject instanceof TaxonInterface && !$subject instanceof TaxonomyInterface){
            throw new \InvalidArgumentException('CustomizableProductInterface, VariantInterface, TaxonInterface, TaxonomyInterface or expected.');
        }

        if ($subject instanceof VariantInterface || $subject instanceof TaxonInterface || $subject instanceof TaxonomyInterface) {
            $variant = $subject;
        }
        else {
            $variant = $subject->getMasterVariant();
        }

        if (true === method_exists($variant, 'getImages')) {
            foreach ($variant->getImages() as $image) {
                if (null === $image->getId()) {
                    $this->uploader->upload($image);
                }
            }
        }
        else {
            if (null === $subject->getId()) {
                $this->uploader->upload($subject);
            }
            elseif ($subject instanceof TaxonInterface || $subject instanceof TaxonomyInterface) {
                $this->uploader->upload($subject);
            }
        }


    }
}
