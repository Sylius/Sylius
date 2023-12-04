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

namespace Sylius\Component\Promotion\Model;

use Doctrine\Common\Collections\Collection;

interface PromotionSubjectInterface
{
    public function getPromotionSubjectTotal(): int;

    /**
     * @return Collection<array-key, PromotionInterface>
     */
    public function getPromotions(): Collection;

    public function hasPromotion(PromotionInterface $promotion): bool;

    public function addPromotion(PromotionInterface $promotion): void;

    public function removePromotion(PromotionInterface $promotion): void;
}
