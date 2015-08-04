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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AverageRatingCalculator implements AverageRatingCalculatorInterface
{
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
            $sum = $sum + $review->getRating();
        }

        return $sum / count($reviews);
    }
}
