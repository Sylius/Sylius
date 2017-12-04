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
    /**
     * @var PromotionCouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var float
     */
    private $ratio;

    /**
     * @param PromotionCouponRepositoryInterface $couponRepository
     * @param float $ratio
     */
    public function __construct(PromotionCouponRepositoryInterface $couponRepository, float $ratio = 0.5)
    {
        $this->couponRepository = $couponRepository;
        $this->ratio = $ratio;
    }

    /**
     * {@inheritdoc}
     */
    public function isGenerationPossible(PromotionCouponGeneratorInstructionInterface $instruction): bool
    {
        $expectedGenerationAmount = $instruction->getAmount();
        $possibleGenerationAmount = $this->calculatePossibleGenerationAmount($instruction);

        return $possibleGenerationAmount >= $expectedGenerationAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getPossibleGenerationAmount(PromotionCouponGeneratorInstructionInterface $instruction): int
    {
        return $this->calculatePossibleGenerationAmount($instruction);
    }

    /**
     * @param PromotionCouponGeneratorInstructionInterface $instruction
     *
     * @return int
     *
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

        $generatedAmount = $this->couponRepository->countByCodeLength($expectedCodeLength);

        return (int) floor((16 ** $expectedCodeLength) * $this->ratio - $generatedAmount);
    }
}
