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

namespace Sylius\Component\Promotion\Action;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

interface PromotionActionCommandInterface
{
    /**
     * @param PromotionSubjectInterface $subject
     * @param array $configuration
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): bool;

    /**
     * @param PromotionSubjectInterface $subject
     * @param array $configuration
     * @param PromotionInterface $promotion
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): void;
}
