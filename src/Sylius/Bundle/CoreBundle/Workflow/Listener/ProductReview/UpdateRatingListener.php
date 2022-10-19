<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\ProductReview;

use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Workflow\Event\Event;

final class UpdateRatingListener
{
    public function __construct(private ReviewableRatingUpdaterInterface $ratingUpdater)
    {
    }

    public function updateFromReview(Event $event): void
    {
        /** @var ReviewInterface $review */
        $review = $event->getSubject();

        $this->ratingUpdater->updateFromReview($review);
    }
}
