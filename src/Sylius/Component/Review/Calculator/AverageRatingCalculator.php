<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Review\Calculator;

use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AverageRatingCalculator implements ReviewableRatingCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ReviewableInterface $reviewable)
    {
        $sum = 0;
        $reviewsNumber = 0;
        $reviews = $reviewable->getReviews();

        /** @var ReviewInterface $review */
        foreach ($reviews as $review) {
            if (ReviewInterface::STATUS_ACCEPTED === $review->getStatus()) {
                ++$reviewsNumber;

                $sum += $review->getRating();
            }
        }

        return 0 !== $reviewsNumber ? $sum / $reviewsNumber : 0;
    }
}
