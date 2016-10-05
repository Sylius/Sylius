<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\ImageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ImageUniqueCodeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($images, Constraint $constraint)
    {
        /** @var ImageInterface[] $images */
        foreach ($images as $key => $image) {
            $filteredImages = $images->filter(function(ImageInterface $imageFromObject) use ($image) {
                return $imageFromObject->getOwner() === $image->getOwner()
                        && $imageFromObject->getCode() === $image->getCode()
                        && $imageFromObject !== $image
                ;
            });

            if(0 !== count($filteredImages)) {
                $this->context->addViolationAt(sprintf('[%d].code', $key), $constraint->message);
            }
        }
    }
}
