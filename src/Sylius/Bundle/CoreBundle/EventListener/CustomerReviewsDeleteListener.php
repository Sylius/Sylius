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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemoverInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class CustomerReviewsDeleteListener
{
    /**
     * @var ReviewerReviewsRemoverInterface
     */
    private $reviewerReviewsRemover;

    /**
     * @param ReviewerReviewsRemoverInterface $reviewerReviewsRemover
     */
    public function __construct(ReviewerReviewsRemoverInterface $reviewerReviewsRemover)
    {
        $this->reviewerReviewsRemover = $reviewerReviewsRemover;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function removeCustomerReviews(GenericEvent $event): void
    {
        $author = $event->getSubject();
        Assert::isInstanceOf($author, ReviewerInterface::class);

        $this->reviewerReviewsRemover->removeReviewerReviews($author);
    }
}
