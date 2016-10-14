<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Promotion\Generator;

use Sylius\Component\Promotion\Repository\PromotionCouponRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
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
     * {@inheritdoc}
     */
    public function __construct(PromotionCouponRepositoryInterface $couponRepository, $ratio = 0.5)
    {
        $this->couponRepository = $couponRepository;
        $this->ratio = $ratio;
    }

    /**
     * {@inheritdoc}
     */
    public function isGenerationPossible(PromotionCouponGeneratorInstructionInterface $instruction)
    {
        $expectedGenerationAmount = $instruction->getAmount();
        $possibleGenerationAmount = $this->calculatePossibleGenerationAmount($instruction);

        return $possibleGenerationAmount >= $expectedGenerationAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getPossibleGenerationAmount(PromotionCouponGeneratorInstructionInterface $instruction)
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
    private function calculatePossibleGenerationAmount(PromotionCouponGeneratorInstructionInterface $instruction)
    {
        $expectedAmount = $instruction->getAmount();
        $expectedCodeLength = $instruction->getCodeLength();

        Assert::allNotNull(
            [$expectedAmount, $expectedCodeLength],
            'Code length or amount cannot be null.'
        );

        $generatedAmount = $this->couponRepository->countByCodeLength($expectedCodeLength);

        return floor(pow(16, $expectedCodeLength) * $this->ratio - $generatedAmount);
    }
}
