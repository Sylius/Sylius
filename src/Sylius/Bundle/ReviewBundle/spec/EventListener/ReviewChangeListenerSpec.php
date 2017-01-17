<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ReviewBundle\EventListener\ReviewChangeListener;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ReviewChangeListenerSpec extends ObjectBehavior
{
    function let(ReviewableRatingUpdaterInterface $averageRatingUpdater)
    {
        $this->beConstructedWith($averageRatingUpdater);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReviewChangeListener::class);
    }

    function it_recalculates_subject_rating_on_accepted_review_deletion(
        $averageRatingUpdater,
        GenericEvent $event,
        ReviewInterface $review,
        ReviewableInterface $reviewSubject
    ) {
        $event->getSubject()->willReturn($review);
        $review->getStatus()->willReturn(ReviewInterface::STATUS_ACCEPTED);
        $review->getReviewSubject()->willReturn($reviewSubject);

        $averageRatingUpdater->update($reviewSubject)->shouldBeCalled();

        $this->recalculateSubjectRating($event);
    }

    function it_does_nothing_if_review_was_new($averageRatingUpdater, GenericEvent $event, ReviewInterface $review)
    {
        $event->getSubject()->willReturn($review);
        $review->getStatus()->willReturn(ReviewInterface::STATUS_NEW);

        $averageRatingUpdater->update(Argument::type(ReviewableInterface::class))->shouldNotBeCalled();

        $this->recalculateSubjectRating($event);
    }

    function it_does_nothing_if_review_was_rejected($averageRatingUpdater, GenericEvent $event, ReviewInterface $review)
    {
        $event->getSubject()->willReturn($review);
        $review->getStatus()->willReturn(ReviewInterface::STATUS_REJECTED);

        $averageRatingUpdater->update(Argument::type(ReviewableInterface::class))->shouldNotBeCalled();

        $this->recalculateSubjectRating($event);
    }

    function it_throws_exception_if_event_subject_is_not_review_object(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject');

        $this
            ->shouldThrow(new UnexpectedTypeException('badObject', ReviewInterface::class))
            ->during('recalculateSubjectRating', [$event])
        ;
    }
}
