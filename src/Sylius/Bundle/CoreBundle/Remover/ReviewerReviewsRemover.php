<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Remover;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ReviewerReviewsRemover implements ReviewerReviewsRemoverInterface
{
    /**
     * @var EntityRepository
     */
    private $reviewRepository;

    /**
     * @var ObjectManager
     */
    private $reviewManager;

    /**
     * @var ReviewableRatingUpdaterInterface
     */
    private $averageRatingUpdater;

    /**
     * @param EntityRepository $reviewRepository
     * @param ObjectManager $reviewManager
     * @param ReviewableRatingUpdaterInterface $averageRatingUpdater
     */
    public function __construct(
        EntityRepository $reviewRepository,
        ObjectManager $reviewManager,
        ReviewableRatingUpdaterInterface $averageRatingUpdater
    ) {
        $this->reviewRepository = $reviewRepository;
        $this->reviewManager = $reviewManager;
        $this->averageRatingUpdater = $averageRatingUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function removeReviewerReviews(ReviewerInterface $author)
    {
        $reviewSubjectsToRecalculate = [];

        foreach ($this->reviewRepository->findBy(['author' => $author]) as $review) {
            $reviewSubjectsToRecalculate = $this->removeReviewsAndExtractSubject($review, $reviewSubjectsToRecalculate);
        }
        $this->reviewManager->flush();

        foreach ($reviewSubjectsToRecalculate as $reviewSubject) {
            $this->averageRatingUpdater->update($reviewSubject);
        }
    }

    /**
     * @param ReviewInterface $review
     * @param ReviewableInterface[] $reviewSubjectsToRecalculate
     *
     * @return array
     */
    private function removeReviewsAndExtractSubject(ReviewInterface $review, array $reviewSubjectsToRecalculate)
    {
        $reviewSubject = $review->getReviewSubject();

        if (!in_array($reviewSubject, $reviewSubjectsToRecalculate)) {
            $reviewSubjectsToRecalculate[] = $reviewSubject;
        }

        $this->reviewManager->remove($review);

        return $reviewSubjectsToRecalculate;
    }
}
