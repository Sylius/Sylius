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

use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OptionsBasedVariantSelectionMethodValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var ProductInterface $value */
        Assert::isInstanceOf($value, ProductInterface::class);

        /** @var OptionsBasedVariantSelectionMethod $constraint */
        Assert::isInstanceOf($constraint, OptionsBasedVariantSelectionMethod::class);

        if (
            ProductInterface::VARIANT_SELECTION_MATCH !== $value->getVariantSelectionMethod() ||
            !$value->getOptions()->isEmpty()
        ) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('variantSelectionMethod')
            ->addViolation()
        ;
    }
}
