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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface ReviewableInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return ReviewInterface[]
     */
    public function getReviews();

    /**
     * @param ReviewInterface $review
     */
    public function addReview(ReviewInterface $review);

    /**
     * @param ReviewInterface $review
     */
    public function removeReview(ReviewInterface $review);

    /**
     * @return float
     */
    public function getAverageRating();

    /**
     * @param float $averageRating
     */
    public function setAverageRating($averageRating);
}
