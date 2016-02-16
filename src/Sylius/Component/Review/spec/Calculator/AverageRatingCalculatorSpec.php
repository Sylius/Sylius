<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Review\Calculator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class AverageRatingCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Review\Calculator\AverageRatingCalculator');
    }

    function it_implements_average_price_calculator_interface()
    {
        $this->shouldImplement(ReviewableRatingCalculatorInterface::class);
    }

    function it_calculates_average_price(
        ReviewableInterface $reviewable,
        ReviewInterface $review1,
        ReviewInterface $review2
    ) {
        $reviewable->getReviews()->willReturn([$review1, $review2]);

        $review1->getStatus()->willReturn(ReviewInterface::STATUS_ACCEPTED)->shouldBeCalled();
        $review2->getStatus()->willReturn(ReviewInterface::STATUS_ACCEPTED)->shouldBeCalled();

        $review1->getRating()->willReturn(4);
        $review2->getRating()->willReturn(5);

        $this->calculate($reviewable)->shouldReturn(4.5);
    }

    function it_returns_zero_if_given_reviewable_object_has_no_reviews(ReviewableInterface $reviewable)
    {
        $reviewable->getReviews()->willReturn([])->shouldBeCalled();

        $this->calculate($reviewable)->shouldReturn(0);
    }

    function it_returns_zero_if_given_reviewable_object_has_reviews_but_none_of_them_is_accepted(
        ArrayCollection $reviews,
        \Iterator $iterator,
        ReviewableInterface $reviewable,
        ReviewInterface $review
    ) {
        $reviewable->getReviews()->willReturn($reviews)->shouldBeCalled();

        $reviews->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, true, false)->shouldBeCalled();
        $iterator->current()->willReturn($review);

        $review->getStatus()->willReturn(ReviewInterface::STATUS_NEW)->shouldBeCalled();

        $iterator->next()->shouldBeCalled();

        $this->calculate($reviewable)->shouldReturn(0);
    }
}
