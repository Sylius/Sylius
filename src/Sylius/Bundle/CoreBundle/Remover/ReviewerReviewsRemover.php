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

namespace Sylius\Bundle\CoreBundle\Remover;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class ReviewerReviewsRemover implements ReviewerReviewsRemoverInterface
{
    /**
     * @param RepositoryInterface<ReviewInterface> $reviewRepository
     */
    public function __construct(
        private RepositoryInterface $reviewRepository,
        private ObjectManager $reviewManager,
        private ReviewableRatingUpdaterInterface $averageRatingUpdater,
    ) {
    }

    public function removeReviewerReviews(ReviewerInterface $author): void
    {
        $reviewSubjectsToRecalculate = [];

        /** @var ReviewInterface $review */
        foreach ($this->reviewRepository->findBy(['author' => $author]) as $review) {
            $reviewSubjectsToRecalculate = $this->removeReviewsAndExtractSubject($review, $reviewSubjectsToRecalculate);
        }
        $this->reviewManager->flush();

        foreach ($reviewSubjectsToRecalculate as $reviewSubject) {
            $this->averageRatingUpdater->update($reviewSubject);
        }
    }

    /**
     * @param array|ReviewableInterface[] $reviewSubjectsToRecalculate
     *
     * @return array|ReviewableInterface[]
     */
    private function removeReviewsAndExtractSubject(ReviewInterface $review, array $reviewSubjectsToRecalculate): array
    {
        $reviewSubject = $review->getReviewSubject();

        if (!in_array($reviewSubject, $reviewSubjectsToRecalculate)) {
            $reviewSubjectsToRecalculate[] = $reviewSubject;
        }

        $this->reviewManager->remove($review);

        return $reviewSubjectsToRecalculate;
    }
}
