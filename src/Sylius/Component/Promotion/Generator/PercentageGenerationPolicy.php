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

use Sylius\Component\Promotion\Repository\CouponRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PercentageGenerationPolicy implements GenerationPolicyInterface
{
    /**
     * @var CouponRepositoryInterface
     */
    private $couponRepository;

    /**
     * @var float
     */
    private $ratio;

    /**
     * {@inheritdoc}
     */
    public function __construct(CouponRepositoryInterface $couponRepository, $ratio = 0.5)
    {
        $this->couponRepository = $couponRepository;
        $this->ratio = $ratio;
    }

    /**
     * {@inheritdoc}
     */
    public function isGenerationPossible(InstructionInterface $instruction)
    {
        $expectedGenerationAmount = $instruction->getAmount();
        $possibleGenerationAmount = $this->calculatePossibleGenerationAmount($instruction);

        return $possibleGenerationAmount >= $expectedGenerationAmount;
    }

    /**
     * {@inheritdoc}
     */
    public function getPossibleGenerationAmount(InstructionInterface $instruction)
    {
        return $this->calculatePossibleGenerationAmount($instruction);
    }

    /**
     * @param InstructionInterface $instruction
     *
     * @return int
     */
    private function calculatePossibleGenerationAmount(InstructionInterface $instruction)
    {
        $expectedAmount = $instruction->getAmount();
        $expectedCodeLength = $instruction->getCodeLength();

        Assert::allNotNull(
            [$expectedAmount, $expectedCodeLength],
            'Code length or amount cannot be null.'
        );
        $generatedAmount = $this->couponRepository->countCouponsByCodeLength($expectedCodeLength);

        return floor(pow(16, $expectedCodeLength) * $this->ratio - $generatedAmount);
    }
}
