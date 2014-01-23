<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Validator;

use Sylius\Bundle\VariableProductBundle\Model\VariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Unique option values combination for variant.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantCombinationValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof VariantInterface) {
            throw new UnexpectedTypeException($value, 'Sylius\Bundle\VariableProductBundle\Model\VariantInterface');
        }

        $variant = $value;
        $product = $variant->getProduct();

        if (!$product->hasVariants() || $variant->isMaster() || !$product->hasOptions()) {
            return;
        }

        $matches = false;
        $combination = array();

        foreach ($variant->getOptions() as $option) {
            $combination[] = $option;
        }

        foreach ($product->getVariants() as $existingVariant) {
            if ($variant === $existingVariant) {
                continue;
            }

            $matches = true;

            foreach ($combination as $option) {
                if (!$existingVariant->hasOption($option)) {
                    $matches = false;
                }
            }

            if ($matches) {
                break;
            }
        }

        if ($matches) {
            $this->context->addViolation($constraint->message);
        }
    }
}
