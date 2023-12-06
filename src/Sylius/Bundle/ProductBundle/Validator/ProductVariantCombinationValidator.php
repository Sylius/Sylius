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

use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductVariantCombination;
use Sylius\Component\Product\Checker\ProductVariantsParityCheckerInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Webmozart\Assert\Assert;

final class ProductVariantCombinationValidator extends ConstraintValidator
{
    public function __construct(private ProductVariantsParityCheckerInterface $variantsParityChecker)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var ProductVariantCombination $constraint */
        Assert::isInstanceOf($constraint, ProductVariantCombination::class);

        if (!$value instanceof ProductVariantInterface) {
            throw new UnexpectedTypeException($value, ProductVariantInterface::class);
        }

        $product = $value->getProduct();

        if ($product === null || !$product->hasVariants() || !$product->hasOptions()) {
            return;
        }

        if ($this->variantsParityChecker->checkParity($value, $product)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
