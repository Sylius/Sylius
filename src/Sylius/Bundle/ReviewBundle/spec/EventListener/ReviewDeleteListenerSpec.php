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
use Sylius\Bundle\ReviewBundle\Updater\ReviewableAverageRatingUpdaterInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewDeleteListenerSpec extends ObjectBehavior
{
    function let(ReviewableAverageRatingUpdaterInterface $reviewableAverageRatingUpdater)
    {
        $this->beConstructedWith($reviewableAverageRatingUpdater);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\EventListener\ReviewDeleteListener');
    }

    function it_recalculates_subject_rating_on_accepted_review_deletion($reviewableAverageRatingUpdater, GenericEvent $event, ReviewInterface $review)
    {
        $event->getSubject()->willReturn($review)->shouldBeCalled();
        $review->getStatus()->willReturn(ReviewInterface::STATUS_ACCEPTED)->shouldBeCalled();

        $reviewableAverageRatingUpdater->update($review)->shouldBeCalled();

        $this->recalculateSubjectRating($event);
    }

    function it_does_nothing_if_review_was_new($reviewableAverageRatingUpdater, GenericEvent $event, ReviewInterface $review)
    {
        $event->getSubject()->willReturn($review)->shouldBeCalled();
        $review->getStatus()->willReturn(ReviewInterface::STATUS_NEW)->shouldBeCalled();

        $reviewableAverageRatingUpdater->update($review)->shouldNotBeCalled();

        $this->recalculateSubjectRating($event);
    }

    function it_does_nothing_if_review_was_rejected($reviewableAverageRatingUpdater, GenericEvent $event, ReviewInterface $review)
    {
        $event->getSubject()->willReturn($review)->shouldBeCalled();
        $review->getStatus()->willReturn(ReviewInterface::STATUS_REJECTED)->shouldBeCalled();

        $reviewableAverageRatingUpdater->update($review)->shouldNotBeCalled();

        $this->recalculateSubjectRating($event);
    }

    function it_throws_exception_if_event_subject_is_not_review_object(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject');

        $this
            ->shouldThrow(new UnexpectedTypeException('badObject', 'Sylius\Component\Review\Model\ReviewInterface'))
            ->during('recalculateSubjectRating', array($event))
        ;
    }
}
