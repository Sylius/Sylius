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

namespace spec\Sylius\Component\Review\Calculator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class AverageRatingCalculatorSpec extends ObjectBehavior
{
    function it_implements_average_price_calculator_interface(): void
    {
        $this->shouldImplement(ReviewableRatingCalculatorInterface::class);
    }

    function it_calculates_average_price(
        ReviewableInterface $reviewable,
        ReviewInterface $review1,
        ReviewInterface $review2
    ): void {
        $reviewable->getReviews()->willReturn(new ArrayCollection([$review1->getWrappedObject(), $review2->getWrappedObject()]));

        $review1->getStatus()->willReturn(ReviewInterface::STATUS_ACCEPTED);
        $review2->getStatus()->willReturn(ReviewInterface::STATUS_ACCEPTED);

        $review1->getRating()->willReturn(4);
        $review2->getRating()->willReturn(5);

        $this->calculate($reviewable)->shouldReturn(4.5);
    }

    function it_returns_zero_if_given_reviewable_object_has_no_reviews(ReviewableInterface $reviewable): void
    {
        $reviewable->getReviews()->willReturn(new ArrayCollection([]))->shouldBeCalled();

        $this->calculate($reviewable)->shouldReturn(0.0);
    }

    function it_returns_zero_if_given_reviewable_object_has_reviews_but_none_of_them_is_accepted(
        ReviewableInterface $reviewable,
        ReviewInterface $review
    ): void {
        $reviewable->getReviews()->willReturn(new ArrayCollection([$review->getWrappedObject()]));
        $review->getStatus()->willReturn(ReviewInterface::STATUS_NEW);

        $this->calculate($reviewable)->shouldReturn(0.0);
    }
}
