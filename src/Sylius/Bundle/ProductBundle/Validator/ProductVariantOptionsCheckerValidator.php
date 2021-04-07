<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Validator;

use Doctrine\ORM\PersistentCollection;
use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductVariantOptionsChecker;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ProductVariantOptionsCheckerValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /** @var ProductVariantOptionsChecker $constraint */
        Assert::isInstanceOf($constraint, ProductVariantOptionsChecker::class);

        if (!$this->context->getRoot()->has('optionValues')) {
            return;
        }

        if (count($value->getOptionValues()) === 0) {
            $this->context->addViolation($constraint->emptyMessage);
            return;
        }

        foreach ($value->getOptionValues() as $option) {
            if ($option === null) {
                $this->context->addViolation($constraint->nullMessage);
                return;
            }
        }
    }
}
