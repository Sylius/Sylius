<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\ProductReview;

use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class UpdateRatingCallback implements AfterAcceptedReviewCallbackInterface
{
    public function __construct(private ReviewableRatingUpdaterInterface $ratingUpdater)
    {
    }

    public function call(ReviewInterface $productReview): void
    {
        $this->ratingUpdater->updateFromReview($productReview);
    }
}
