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

namespace Sylius\Bundle\TaxonomyBundle\Validator\Constraints;

use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class TaxonParentRelationValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var TaxonParentRelation $constraint */
        Assert::isInstanceOf($constraint, TaxonParentRelation::class);

        if (!$value instanceof TaxonInterface) {
            return;
        }

        $taxon = $value;
        $parent = $taxon->getParent();
        if (null === $parent) {
            return;
        }

        if ($parent === $taxon) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('parent')
                ->addViolation();

            return;
        }

        if (null !== $taxon->getId() && null !== $parent->getId() && $taxon->getId() === $parent->getId()) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('parent')
                ->addViolation();

            return;
        }

        $current = $parent->getParent();
        while (null !== $current) {
            if ($current === $taxon) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->atPath('parent')
                    ->addViolation();

                return;
            }

            $current = $current->getParent();
        }
    }
}
