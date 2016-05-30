<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariationBundle\Validator;

use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantCombinationValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof VariantInterface) {
            throw new UnexpectedTypeException($value, VariantInterface::class);
        }

        $variable = $value->getObject();
        if (!$variable->hasVariants() || !$variable->hasOptions()) {
            return;
        }

        if ($this->matches($value, $variable)) {
            $this->context->addViolation($constraint->message);
        }
    }

    /**
     * @param VariantInterface  $variant
     * @param VariableInterface $variable
     *
     * @return bool
     */
    private function matches(VariantInterface $variant, VariableInterface $variable)
    {
        foreach ($variable->getVariants() as $existingVariant) {
            if ($variant === $existingVariant) {
                continue;
            }

            $matches = true;

            if (!$variant->getOptions()->count()) {
                continue;
            }

            foreach ($variant->getOptions() as $option) {
                if (!$existingVariant->hasOption($option)) {
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
