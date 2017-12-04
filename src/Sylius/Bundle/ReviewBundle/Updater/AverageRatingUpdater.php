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

namespace Sylius\Bundle\ReviewBundle\Updater;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

class AverageRatingUpdater implements ReviewableRatingUpdaterInterface
{
    /**
     * @var ReviewableRatingCalculatorInterface
     */
    private $averageRatingCalculator;

    /**
     * @var ObjectManager
     */
    private $reviewSubjectManager;

    /**
     * @param ReviewableRatingCalculatorInterface $averageRatingCalculator
     * @param ObjectManager $reviewSubjectManager
     */
    public function __construct(
        ReviewableRatingCalculatorInterface $averageRatingCalculator,
        ObjectManager $reviewSubjectManager
    ) {
        $this->averageRatingCalculator = $averageRatingCalculator;
        $this->reviewSubjectManager = $reviewSubjectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function update(ReviewableInterface $reviewSubject): void
    {
        $this->modifyReviewSubjectAverageRating($reviewSubject);
    }

    /**
     * {@inheritdoc}
     */
    public function updateFromReview(ReviewInterface $review): void
    {
        $this->modifyReviewSubjectAverageRating($review->getReviewSubject());
    }

    /**
     * @param ReviewableInterface $reviewSubject
     */
    private function modifyReviewSubjectAverageRating(ReviewableInterface $reviewSubject): void
    {
        $averageRating = $this->averageRatingCalculator->calculate($reviewSubject);

        $reviewSubject->setAverageRating($averageRating);
        $this->reviewSubjectManager->flush($reviewSubject);
    }
}
