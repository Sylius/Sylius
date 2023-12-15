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

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class PromotionSubject implements ResourceInterface, PromotionSubjectInterface
{
    private int $id;

    /** @var Collection<array-key, PromotionInterface> */
    protected $promotions;

    public function getId(): int
    {
        return $this->id;
    }

    public function hasPromotion(PromotionInterface $promotion): bool
    {
        return $this->promotions->contains($promotion);
    }

    public function addPromotion(PromotionInterface $promotion): void
    {
        if (!$this->hasPromotion($promotion)) {
            $this->promotions->add($promotion);
        }
    }

    public function removePromotion(PromotionInterface $promotion): void
    {
        if ($this->hasPromotion($promotion)) {
            $this->promotions->removeElement($promotion);
        }
    }

    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function getPromotionSubjectTotal(): int
    {
        return 0;
    }
}
