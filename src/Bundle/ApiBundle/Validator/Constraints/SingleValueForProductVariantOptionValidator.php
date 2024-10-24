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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class SingleValueForProductVariantOptionValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var ProductVariantInterface $value */
        Assert::isInstanceOf($value, ProductVariantInterface::class);

        /** @var SingleValueForProductVariantOption $constraint */
        Assert::isInstanceOf($constraint, SingleValueForProductVariantOption::class);

        $map = array_map(fn (ProductOptionValueInterface $productOptionValue) => $productOptionValue->getOptionCode(), $value->getOptionValues()->toArray());
        /** @var array<string, int> $flippedMap */
        $flippedMap = array_flip($map);
        if (count($map) !== count($flippedMap)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
