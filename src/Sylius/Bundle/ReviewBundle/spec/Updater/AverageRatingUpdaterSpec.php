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

namespace spec\Sylius\Bundle\ReviewBundle\Updater;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class AverageRatingUpdaterSpec extends ObjectBehavior
{
    function let(ReviewableRatingCalculatorInterface $averageRatingCalculator, ObjectManager $reviewSubjectManager): void
    {
        $this->beConstructedWith($averageRatingCalculator, $reviewSubjectManager);
    }

    function it_implements_product_average_rating_updater_interface(): void
    {
        $this->shouldImplement(ReviewableRatingUpdaterInterface::class);
    }

    function it_updates_review_subject_average_rating(
        ReviewableRatingCalculatorInterface $averageRatingCalculator,
        ObjectManager $reviewSubjectManager,
        ReviewableInterface $reviewSubject
    ): void {
        $averageRatingCalculator->calculate($reviewSubject)->willReturn(4.5);

        $reviewSubject->setAverageRating(4.5)->shouldBeCalled();

        $reviewSubjectManager->flush()->shouldBeCalled();

        $this->update($reviewSubject);
    }

    function it_updates_review_subject_average_rating_from_review(
        ReviewableRatingCalculatorInterface $averageRatingCalculator,
        ObjectManager $reviewSubjectManager,
        ReviewableInterface $reviewSubject,
        ReviewInterface $review
    ): void {
        $review->getReviewSubject()->willReturn($reviewSubject);
        $averageRatingCalculator->calculate($reviewSubject)->willReturn(4.5);

        $reviewSubject->setAverageRating(4.5)->shouldBeCalled();

        $reviewSubjectManager->flush()->shouldBeCalled();

        $this->updateFromReview($review);
    }
}
