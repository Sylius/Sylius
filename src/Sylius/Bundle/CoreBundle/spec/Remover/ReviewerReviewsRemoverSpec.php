<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Remover;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemover;
use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemoverInterface;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ReviewerReviewsRemoverSpec extends ObjectBehavior
{
    function let(
        EntityRepository $reviewRepository,
        ObjectManager $reviewManager,
        ReviewableRatingUpdaterInterface $averageRatingUpdater
    ) {
        $this->beConstructedWith($reviewRepository, $reviewManager, $averageRatingUpdater);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReviewerReviewsRemover::class);
    }

    function it_implements_reviewer_reviews_remover_interface()
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
    ) {
        $reviewRepository->findBy(['author' => $author])->willReturn([$review]);
        $review->getReviewSubject()->willReturn($reviewSubject);

        $reviewManager->remove($review)->shouldBeCalled();
        $reviewManager->flush()->shouldBeCalled();

        $averageRatingUpdater->update($reviewSubject)->shouldBeCalled();

        $this->removeReviewerReviews($author);
    }
}
