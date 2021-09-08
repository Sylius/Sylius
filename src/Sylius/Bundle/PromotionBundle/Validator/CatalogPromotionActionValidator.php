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

namespace Sylius\Bundle\PromotionBundle\Validator;

use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CatalogPromotionActionValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        /** @var CatalogPromotionAction $constraint */
        Assert::isInstanceOf($constraint, CatalogPromotionAction::class);

        /** @var CatalogPromotionActionInterface $value */
        if ($value->getType() !== CatalogPromotionActionInterface::TYPE_PERCENTAGE_DISCOUNT) {
            $this->context->buildViolation($constraint->invalidType)->atPath('type')->addViolation();

            return;
        }

        $configuration = $value->getConfiguration();
        if (!array_key_exists('amount', $configuration)) {
            $this->context->buildViolation($constraint->invalidDiscount)->atPath('configuration.amount')->addViolation();

            return;
        }

        if ($configuration['amount'] < 0 || $configuration['amount'] > 1) {
            $this->context->buildViolation($constraint->notInRangeDiscount)->atPath('configuration.amount')->addViolation();
        }
    }
}
