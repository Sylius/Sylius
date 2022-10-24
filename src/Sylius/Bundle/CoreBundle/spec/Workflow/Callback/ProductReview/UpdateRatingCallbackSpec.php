<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\ProductReview;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\ProductReview\UpdateRatingCallback;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class UpdateRatingCallbackSpec extends ObjectBehavior
{
    function let(ReviewableRatingUpdaterInterface $ratingUpdater): void
    {
        $this->beConstructedWith($ratingUpdater);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(UpdateRatingCallback::class);
    }

    function it_updates_rating(
        ReviewInterface $productReview,
        ReviewableRatingUpdaterInterface $ratingUpdater,
    ): void {
        $ratingUpdater->updateFromReview($productReview)->shouldBeCalled();

        $this->call($productReview);
    }
}
