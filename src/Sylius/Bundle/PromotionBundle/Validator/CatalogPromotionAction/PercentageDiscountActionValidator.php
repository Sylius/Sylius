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

namespace Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction;

use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Webmozart\Assert\Assert;

final class PercentageDiscountActionValidator implements ActionValidatorInterface
{
    public function validate(array $configuration, Constraint $constraint, ExecutionContextInterface $context): void
    {
        /** @var CatalogPromotionAction $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionAction::class);

        if (!array_key_exists('amount', $configuration)) {
            $context->buildViolation($constraint->notNumberOrEmpty)->atPath('configuration.amount')->addViolation();

            return;
        }

        if (!is_float($configuration['amount']) && !is_integer($configuration['amount'])) {
            $context->buildViolation($constraint->notNumberOrEmpty)->atPath('configuration.amount')->addViolation();

            return;
        }

        if ($configuration['amount'] < 0 || $configuration['amount'] > 1) {
            $context->buildViolation($constraint->notInRangeDiscount)->atPath('configuration.amount')->addViolation();
        }
    }
}
