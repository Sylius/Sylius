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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ImageUniqueCodeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($images, Constraint $constraint)
    {
        $imagesCodes = [];

        /** @var ImageInterface[] $images */
        foreach ($images as $key => $image) {
            if (!array_key_exists($image->getCode(), $imagesCodes)) {
                $imagesCodes[$image->getCode()] = $key;
                continue;
            }

            $this->context->buildViolation($constraint->message)->atPath(sprintf('[%d].code', $key))->addViolation();
            if (false !== $imagesCodes[$image->getCode()]) {
                $this->context->buildViolation($constraint->message)->atPath(sprintf('[%d].code', $imagesCodes[$image->getCode()]))->addViolation();
                $imagesCodes[$image->getCode()] = false;
            }
        }
    }
}
