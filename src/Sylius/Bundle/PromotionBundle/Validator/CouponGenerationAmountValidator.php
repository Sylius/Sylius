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

use Sylius\Bundle\PromotionBundle\Validator\Constraints\CouponPossibleGenerationAmount;
use Sylius\Component\Promotion\Generator\GenerationPolicyInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstructionInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CouponGenerationAmountValidator extends ConstraintValidator
{
    /**
     * @var GenerationPolicyInterface
     */
    private $generationPolicy;

    /**
     * @param GenerationPolicyInterface $generationPolicy
     */
    public function __construct(GenerationPolicyInterface $generationPolicy)
    {
        $this->generationPolicy = $generationPolicy;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($instruction, Constraint $constraint)
    {
        if (null === $instruction->getCodeLength() || null === $instruction->getAmount()) {
            return;
        }

        /** @var PromotionCouponGeneratorInstructionInterface $value */
        Assert::isInstanceOf($instruction, PromotionCouponGeneratorInstructionInterface::class);

        /** @var CouponPossibleGenerationAmount $constraint */
        Assert::isInstanceOf($constraint, CouponPossibleGenerationAmount::class);

        if (!$this->generationPolicy->isGenerationPossible($instruction)) {
            $this->context->addViolation(
                $constraint->message,
                array(
                    '%expectedAmount%' => $instruction->getAmount(),
                    '%codeLength%' => $instruction->getCodeLength(),
                    '%possibleAmount%' => $this->generationPolicy->getPossibleGenerationAmount($instruction)
                )
            );
        }
    }
}
