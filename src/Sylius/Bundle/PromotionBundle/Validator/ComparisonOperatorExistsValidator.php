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

namespace Sylius\Bundle\PromotionBundle\Validator;

use Sylius\Bundle\PromotionBundle\Validator\Constraints\ComparisonOperatorExists;
use Sylius\Component\Promotion\Checker\Comparison\ComparisonOperatorMatcherInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ComparisonOperatorExistsValidator extends ConstraintValidator
{
    public function __construct(private ComparisonOperatorMatcherInterface $comparisonOperatorMatcher)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ComparisonOperatorExists) {
            throw new UnexpectedTypeException($constraint, ComparisonOperatorExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $availableComparisonOperators = $this->comparisonOperatorMatcher->getAvailableComparisonOperators();
        if (in_array($value, $availableComparisonOperators, true)) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->setParameter('{{ available_comparison_operators }}', implode(', ', $availableComparisonOperators))
            ->addViolation()
        ;
    }
}
