<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\ProductReview;

use Sylius\Bundle\CoreBundle\Workflow\Callback\ProductReview\AfterAcceptedReviewCallbackInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class AfterAcceptedReviewListener
{
    /** @param AfterAcceptedReviewCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
        Assert::allIsInstanceOf($callbacks, AfterAcceptedReviewCallbackInterface::class);
    }

    public function call(Event $event): void
    {
        /** @var ReviewInterface $productReview */
        $productReview = $event->getSubject();

        foreach ($this->callbacks as $callback) {
            $callback->call($productReview);
        }
    }
}
