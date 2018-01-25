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

namespace Sylius\Component\Promotion\Model;

use Doctrine\Common\Collections\Collection;

interface PromotionSubjectInterface
{
    /**
     * @return int
     */
    public function getPromotionSubjectTotal(): int;

    /**
     * @return Collection|PromotionInterface[]
     */
    public function getPromotions(): Collection;

    /**
     * @param PromotionInterface $promotion
     *
     * @return bool
     */
    public function hasPromotion(PromotionInterface $promotion): bool;

    /**
     * @param PromotionInterface $promotion
     */
    public function addPromotion(PromotionInterface $promotion): void;

    /**
     * @param PromotionInterface $promotion
     */
    public function removePromotion(PromotionInterface $promotion): void;
}
