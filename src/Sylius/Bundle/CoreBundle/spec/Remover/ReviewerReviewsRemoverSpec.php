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

namespace spec\Sylius\Bundle\CoreBundle\Remover;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemoverInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewerReviewsRemoverSpec extends ObjectBehavior
{
    function let(
        EntityRepository $reviewRepository,
        ObjectManager $reviewManager,
        ReviewableRatingUpdaterInterface $averageRatingUpdater
    ): void {
        $this->beConstructedWith($reviewRepository, $reviewManager, $averageRatingUpdater);
    }

    function it_implements_reviewer_reviews_remover_interface(): void
    {
        $this->shouldImplement(ReviewerReviewsRemoverInterface::class);
    }

    function it_removes_soft_deleted_customer_reviews_and_recalculates_their_product_ratings(
        $averageRatingUpdater,
        $reviewRepository,
        $reviewManager,
        ReviewerInterface $author,
        ReviewableInterface $reviewSubject,
        ReviewInterface $review
    ): void {
        $reviewRepository->findBy(['author' => $author])->willReturn([$review]);
        $review->getReviewSubject()->willReturn($reviewSubject);

        $reviewManager->remove($review)->shouldBeCalled();
        $reviewManager->flush()->shouldBeCalled();

        $averageRatingUpdater->update($reviewSubject)->shouldBeCalled();

        $this->removeReviewerReviews($author);
    }
}
