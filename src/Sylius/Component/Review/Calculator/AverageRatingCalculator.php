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

use Sylius\Component\Review\Model\Reviewable;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AverageRatingCalculator implements AverageRatingCalculatorInterface
{
    /**
     * @var int
     */
    private $reviewsNumber = 0;

    /**
     * {@inheritdoc}
     */
    public function calculate(Reviewable $reviewable)
    {
        if (0 === count($reviews = $reviewable->getReviews())) {
            return 0;
        }

        $sum = 0.0;

        foreach ($reviews as $review) {
            $sum = $this->addReviewRatingIfAccepted($sum, $review);
        }

        if (0 === $this->reviewsNumber) {
            return 0;
        }

        return $sum / $this->reviewsNumber;
    }

    /**
     * @param float           $sum
     * @param ReviewInterface $review
     *
     * @return float
     */
    private function addReviewRatingIfAccepted($sum, ReviewInterface $review)
    {
        if (ReviewInterface::STATUS_ACCEPTED === $review->getStatus()) {
            $this->reviewsNumber++;

            return $sum + $review->getRating();
        }

        return $sum;
    }
}
