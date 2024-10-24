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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\CouponPossibleGenerationAmount;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CouponGenerationAmountValidator extends ConstraintValidator
{
    public function __construct(private GenerationPolicyInterface $generationPolicy)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value->getCodeLength() || null === $value->getAmount()) {
            return;
        }

        /** @var ReadablePromotionCouponGeneratorInstructionInterface $value */
        Assert::isInstanceOf($value, ReadablePromotionCouponGeneratorInstructionInterface::class);

        /** @var CouponPossibleGenerationAmount $constraint */
        Assert::isInstanceOf($constraint, CouponPossibleGenerationAmount::class);

        if (!$this->generationPolicy->isGenerationPossible($value)) {
            $this->context->addViolation(
                $constraint->message,
                [
                    '%expectedAmount%' => $value->getAmount(),
                    '%codeLength%' => $value->getCodeLength(),
                    '%possibleAmount%' => $this->generationPolicy->getPossibleGenerationAmount($value),
                ],
            );
        }
    }
}
