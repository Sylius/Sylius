<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ReviewBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
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
        ReviewableRatingUpdaterInterface $averageRatingUpdater,
        LifecycleEventArgs $event,
        ReviewInterface $review,
        ReviewableInterface $reviewSubject,
    ): void {
        $event->getObject()->willReturn($review);
        $review->getReviewSubject()->willReturn($reviewSubject);

        $averageRatingUpdater->update($reviewSubject)->shouldBeCalled();

        $this->recalculateSubjectRating($event);
    }

    function it_removes_a_review_from_a_review_subject_on_the_post_remove_event(
        ReviewableRatingUpdaterInterface $averageRatingUpdater,
        ReviewInterface $review,
        ReviewableInterface $reviewSubject,
        EntityManagerInterface $entityManager,
    ): void {
        $event = new PostRemoveEventArgs($review->getWrappedObject(), $entityManager->getWrappedObject());
        $review->getReviewSubject()->willReturn($reviewSubject);

        $reviewSubject->removeReview($review)->shouldBeCalled();
        $averageRatingUpdater->update($reviewSubject)->shouldBeCalled();

        $this->recalculateSubjectRating($event);
    }

    function it_does_nothing_if_event_subject_is_not_review_object(
        ReviewableRatingUpdaterInterface $averageRatingUpdater,
        LifecycleEventArgs $event,
    ): void {
        $event->getObject()->willReturn('badObject');

        $averageRatingUpdater->update(Argument::type(ReviewableInterface::class))->shouldNotBeCalled();

        $this->recalculateSubjectRating($event);
    }
}
