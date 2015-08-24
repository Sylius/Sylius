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
use Sylius\Component\Review\Calculator\AverageRatingCalculatorInterface;
use Sylius\Component\Review\Model\Reviewable;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewableAverageRatingUpdaterSpec extends ObjectBehavior
{
    function let(AverageRatingCalculatorInterface $averageRatingCalculator, ObjectManager $reviewSubjectManager)
    {
        $this->beConstructedWith($averageRatingCalculator, $reviewSubjectManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\Updater\ReviewableAverageRatingUpdater');
    }

    function it_implements_product_average_rating_updater_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ReviewBundle\Updater\ReviewableAverageRatingUpdaterInterface');
    }

    function it_updates_review_subject_average_rating(
        $averageRatingCalculator,
        $reviewSubjectManager,
        Reviewable $reviewSubject,
        ReviewInterface $review
    ) {
        $review->getProduct()->willReturn($reviewSubject)->shouldBeCalled();
        $averageRatingCalculator->calculate($reviewSubject)->willReturn(4.5)->shouldBeCalled();

        $reviewSubject->setAverageRating(4.5)->shouldBeCalled();
        $reviewSubjectManager->flush($reviewSubject)->shouldBeCalled();

        $this->update($review);
    }
}
