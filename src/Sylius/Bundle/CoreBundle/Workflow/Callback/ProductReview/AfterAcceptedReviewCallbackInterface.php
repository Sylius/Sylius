<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Callback\ProductReview;

use Sylius\Component\Review\Model\ReviewInterface;

interface AfterAcceptedReviewCallbackInterface
{
    public function call(ReviewInterface $productReview): void;
}
