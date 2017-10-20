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

namespace Sylius\Component\Review\Model;

use Doctrine\Common\Collections\Collection;

interface ReviewableInterface
{
    /**
     * @return string
     */
    public function getName(): ?string;

    /**
     * @return Collection|ReviewInterface[]
     */
    public function getReviews(): Collection;

    /**
     * @param ReviewInterface $review
     */
    public function addReview(ReviewInterface $review): void;

    /**
     * @param ReviewInterface $review
     */
    public function removeReview(ReviewInterface $review): void;

    /**
     * @return float|null
     */
    public function getAverageRating(): ?float;

    /**
     * @param float $averageRating
     */
    public function setAverageRating(float $averageRating): void;
}
