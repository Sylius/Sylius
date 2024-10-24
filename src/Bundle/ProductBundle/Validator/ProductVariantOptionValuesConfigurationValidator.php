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

namespace Sylius\Bundle\ProductBundle\Validator;

use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductVariantOptionValuesConfiguration;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ProductVariantOptionValuesConfigurationValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var ProductVariantInterface $value */
        Assert::isInstanceOf($value, ProductVariantInterface::class);

        /** @var ProductVariantOptionValuesConfiguration $constraint */
        Assert::isInstanceOf($constraint, ProductVariantOptionValuesConfiguration::class);

        $product = $value->getProduct();
        if ($product === null || !$product->hasOptions()) {
            return;
        }

        $requiredOptionCodes = array_map(
            fn (ProductOptionInterface $productOption) => $productOption->getCode(),
            $product->getOptions()->toArray(),
        );
        $variantOptionCodes = array_map(
            fn (ProductOptionValueInterface $productOptionValue) => $productOptionValue->getOptionCode(),
            $value->getOptionValues()->toArray(),
        );

        if (!empty(array_diff($requiredOptionCodes, $variantOptionCodes))) {
            $this->context->addViolation($constraint->message);
        }
    }
}
