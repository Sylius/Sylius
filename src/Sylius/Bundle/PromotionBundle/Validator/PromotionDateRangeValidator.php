<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Validator;

use Sylius\Bundle\PromotionBundle\Validator\Constraints\PromotionDateRange;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PromotionDateRangeValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        /** @var PromotionInterface $value */
        Assert::isInstanceOf($value, PromotionInterface::class);

        /** @var PromotionDateRange $constraint */
        Assert::isInstanceOf($constraint, PromotionDateRange::class);

        if (null === $value->getStartsAt() || null === $value->getEndsAt()) {
            return;
        }

        if ($value->getStartsAt()->getTimestamp() > $value->getEndsAt()->getTimestamp()) {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath('endsAt')
                ->addViolation()
            ;
        }
    }
}
