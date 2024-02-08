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

namespace Sylius\Bundle\ReviewBundle\Updater;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

class AverageRatingUpdater implements ReviewableRatingUpdaterInterface
{
    public function __construct(
        private ReviewableRatingCalculatorInterface $averageRatingCalculator,
        private ObjectManager $reviewSubjectManager,
    ) {
    }

    public function update(ReviewableInterface $reviewSubject): void
    {
        $this->modifyReviewSubjectAverageRating($reviewSubject);
    }

    public function updateFromReview(ReviewInterface $review): void
    {
        $this->modifyReviewSubjectAverageRating($review->getReviewSubject());
    }

    private function modifyReviewSubjectAverageRating(ReviewableInterface $reviewSubject): void
    {
        $averageRating = $this->averageRatingCalculator->calculate($reviewSubject);

        $reviewSubject->setAverageRating($averageRating);

        $this->reviewSubjectManager->flush();
    }
}
