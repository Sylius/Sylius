<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemoverInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
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
     */
    public function removeCustomerReviews(GenericEvent $event)
    {
        $author = $event->getSubject();
        if (!$author instanceof ReviewerInterface) {
            throw new UnexpectedTypeException($author, ReviewerInterface::class);
        }

        $this->reviewerReviewsRemover->removeReviewerReviews($author);
    }
}
