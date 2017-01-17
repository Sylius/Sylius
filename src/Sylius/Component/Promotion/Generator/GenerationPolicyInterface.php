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

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface GenerationPolicyInterface
{
    /**
     * @param PromotionCouponGeneratorInstructionInterface $instruction
     *
     * @return bool
     */
    public function isGenerationPossible(PromotionCouponGeneratorInstructionInterface $instruction);

    /**
     * @param PromotionCouponGeneratorInstructionInterface $instruction
     *
     * @return int
     */
    public function getPossibleGenerationAmount(PromotionCouponGeneratorInstructionInterface $instruction);
}
