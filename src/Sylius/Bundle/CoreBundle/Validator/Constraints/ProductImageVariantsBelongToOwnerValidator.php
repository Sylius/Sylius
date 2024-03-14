<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ProductImageVariantsBelongToOwnerValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ProductImageInterface::class);
        Assert::isInstanceOf($constraint, ProductImageVariantsBelongToOwner::class);

        /** @var ProductInterface $owner */
        $owner = $value->getOwner();

        foreach ($value->getProductVariants() as $productVariant) {
            if (!$owner->hasVariant($productVariant)) {
                $this->context->addViolation($constraint->message, [
                    '%productVariantCode%' => $productVariant->getCode(),
                    '%ownerCode%' => $owner->getCode(),
                ]);
            }
        }
    }
}
