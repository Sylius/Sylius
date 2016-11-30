<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\Updater;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ReviewBundle\Updater\AverageRatingUpdater;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class AverageRatingUpdaterSpec extends ObjectBehavior
{
    function let(ReviewableRatingCalculatorInterface $averageRatingCalculator, ObjectManager $reviewSubjectManager)
    {
        $this->beConstructedWith($averageRatingCalculator, $reviewSubjectManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AverageRatingUpdater::class);
    }

    function it_implements_product_average_rating_updater_interface()
    {
        $this->shouldImplement(ReviewableRatingUpdaterInterface::class);
    }

    function it_updates_review_subject_average_rating(
        $averageRatingCalculator,
        $reviewSubjectManager,
        ReviewableInterface $reviewSubject
    ) {
        $averageRatingCalculator->calculate($reviewSubject)->willReturn(4.5);

        $reviewSubject->setAverageRating(4.5)->shouldBeCalled();
        $reviewSubjectManager->flush($reviewSubject)->shouldBeCalled();

        $this->update($reviewSubject);
    }

    function it_updates_review_subject_average_rating_from_review(
        $averageRatingCalculator,
        $reviewSubjectManager,
        ReviewableInterface $reviewSubject,
        ReviewInterface $review
    ) {
        $review->getReviewSubject()->willReturn($reviewSubject);
        $averageRatingCalculator->calculate($reviewSubject)->willReturn(4.5);

        $reviewSubject->setAverageRating(4.5)->shouldBeCalled();
        $reviewSubjectManager->flush($reviewSubject)->shouldBeCalled();

        $this->updateFromReview($review);
    }
}
