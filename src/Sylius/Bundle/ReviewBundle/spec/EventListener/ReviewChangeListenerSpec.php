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

namespace spec\Sylius\Bundle\ReviewBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewChangeListenerSpec extends ObjectBehavior
{
    function let(ReviewableRatingUpdaterInterface $averageRatingUpdater): void
    {
        $this->beConstructedWith($averageRatingUpdater);
    }

    function it_recalculates_subject_rating_on_accepted_review_deletion(
        $averageRatingUpdater,
        LifecycleEventArgs $event,
        ReviewInterface $review,
        ReviewableInterface $reviewSubject
    ): void {
        $event->getObject()->willReturn($review);
        $review->getReviewSubject()->willReturn($reviewSubject);

        $averageRatingUpdater->update($reviewSubject)->shouldBeCalled();

        $this->recalculateSubjectRating($event);
    }

    function it_does_nothing_if_event_subject_is_not_review_object($averageRatingUpdater, LifecycleEventArgs $event): void
    {
        $event->getObject()->willReturn('badObject');

        $averageRatingUpdater->update(Argument::type(ReviewableInterface::class))->shouldNotBeCalled();

        $this->recalculateSubjectRating($event);
    }
}
