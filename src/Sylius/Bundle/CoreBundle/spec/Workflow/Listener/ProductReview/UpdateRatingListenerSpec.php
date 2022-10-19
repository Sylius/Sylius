<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\ProductReview;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Listener\ProductReview\UpdateRatingListener;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Workflow\Event\Event;

final class UpdateRatingListenerSpec extends ObjectBehavior
{
    function let(ReviewableRatingUpdaterInterface $ratingUpdater): void
    {
        $this->beConstructedWith($ratingUpdater);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(UpdateRatingListener::class);
    }

    function it_updates_from_review(
        Event $event,
        ReviewInterface $review,
        ReviewableRatingUpdaterInterface $ratingUpdater,
    ): void {
        $event->getSubject()->willReturn($review);

        $ratingUpdater->updateFromReview($review)->shouldBeCalled();

        $this->updateFromReview($event);
    }
}
