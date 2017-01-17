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

use Sylius\Component\Product\Checker\ProductVariantsParityCheckerInterface;
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
     * @var ProductVariantsParityCheckerInterface
     */
    private $variantsParityChecker;

    /**
     * @param ProductVariantsParityCheckerInterface $variantsParityChecker
     */
    public function __construct(ProductVariantsParityCheckerInterface $variantsParityChecker)
    {
        $this->variantsParityChecker = $variantsParityChecker;
    }

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

        if ($this->variantsParityChecker->checkParity($value, $product)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
