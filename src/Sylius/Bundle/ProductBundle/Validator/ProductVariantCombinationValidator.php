<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Validator;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductVariantCombinationValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ProductVariantInterface) {
            throw new UnexpectedTypeException($value, ProductVariantInterface::class);
        }

        $product = $value->getProduct();
        if (!$product->hasVariants() || !$product->hasOptions()) {
            return;
        }

        if ($this->matches($value, $product)) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * @param ProductVariantInterface  $variant
     * @param ProductInterface $variable
     *
     * @return bool
     */
    private function matches(ProductVariantInterface $variant, ProductInterface $variable)
    {
        foreach ($variable->getVariants() as $existingVariant) {
            if ($variant === $existingVariant || !$variant->getOptionValues()->count()) {
                continue;
            }

            $matches = true;

            foreach ($variant->getOptionValues() as $optionValue) {
                if (!$existingVariant->hasOptionValue($optionValue)) {
                    $matches = false;
                }
            }

            if ($matches) {
                return true;
            }
        }

        return false;
    }
}
