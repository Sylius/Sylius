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

namespace Sylius\Component\Promotion\Generator;

use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Webmozart\Assert\Assert;

final class PercentageGenerationPolicy implements GenerationPolicyInterface
{
    public function __construct(private PromotionCouponRepositoryInterface $couponRepository, private float $ratio = 0.5)
    {
    }

    public function isGenerationPossible(PromotionCouponGeneratorInstructionInterface $instruction): bool
    {
        $expectedGenerationAmount = $instruction->getAmount();
        $possibleGenerationAmount = $this->calculatePossibleGenerationAmount($instruction);

        return $possibleGenerationAmount >= $expectedGenerationAmount;
    }

    public function getPossibleGenerationAmount(PromotionCouponGeneratorInstructionInterface $instruction): int
    {
        return $this->calculatePossibleGenerationAmount($instruction);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function calculatePossibleGenerationAmount(PromotionCouponGeneratorInstructionInterface $instruction): int
    {
        $expectedAmount = $instruction->getAmount();
        $expectedCodeLength = $instruction->getCodeLength();

        Assert::allNotNull(
            [$expectedAmount, $expectedCodeLength],
            'Code length or amount cannot be null.'
        );

        $generatedAmount = $this->couponRepository->countByCodeLength(
            $expectedCodeLength,
            $instruction->getPrefix(),
            $instruction->getSuffix()
        );

        $codeCombination = 16 ** $expectedCodeLength * $this->ratio;
        if ($codeCombination >= \PHP_INT_MAX) {
            return \PHP_INT_MAX - $generatedAmount;
        }

        return (int) $codeCombination - $generatedAmount;
    }
}
