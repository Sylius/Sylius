<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Review\Model;

use Doctrine\Common\Collections\Collection;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface Reviewable
{
    /**
     * @return Collection|ReviewInterface[]
     */
    public function getReviews();

    /**
     * @param Collection $reviews
     */
    public function setReviews(Collection $reviews);

    /**
     * @param ReviewInterface $review
     */
    public function addReview(ReviewInterface $review);

    /**
     * @param ReviewInterface $review
     */
    public function removeReview(ReviewInterface $review);

    /**
     * @param float $averageRating
     */
    public function setAverageRating($averageRating);

    /**
     * @return float
     */
    public function getAverageRating();
}
