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
use Prophecy\Argument;
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
        $this->shouldImplement('Sylius\Component\Review\Calculator\AverageRatingCalculatorInterface');
    }

    function it_calculates_average_price(
        ArrayCollection $reviews,
        \Iterator $iterator,
        ReviewableInterface $reviewable,
        ReviewInterface $review1,
        ReviewInterface $review2
    ) {
        $reviewable->getReviews()->willReturn($reviews)->shouldBeCalled();

        $reviews->count()->willReturn(2);
        $reviews->getIterator()->willReturn($iterator);
        $iterator->rewind()->shouldBeCalled();
        $iterator->valid()->willReturn(true, true, false)->shouldBeCalled();
        $iterator->current()->willReturn($review1, $review2);

        $review1->getStatus()->willReturn(ReviewInterface::STATUS_ACCEPTED)->shouldBeCalled();
        $review2->getStatus()->willReturn(ReviewInterface::STATUS_ACCEPTED)->shouldBeCalled();

        $iterator->next()->shouldBeCalled();

        $review1->getRating()->willReturn(4);
        $review2->getRating()->willReturn(5);

        $this->calculate($reviewable)->shouldReturn(4.5);
    }

    function it_returns_zero_if_given_reviewable_object_has_no_reviews(ReviewableInterface $reviewable)
    {
        $reviewable->getReviews()->willReturn(array())->shouldBeCalled();

        $this->calculate($reviewable)->shouldReturn(0);
    }
}
