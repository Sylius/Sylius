<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\EventListener;

use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ReviewChangeListener
{
    /**
     * @var ReviewableRatingUpdaterInterface
     */
    private $averageRatingUpdater;

    /**
     * @param ReviewableRatingUpdaterInterface $averageRatingUpdater
     */
    public function __construct(ReviewableRatingUpdaterInterface $averageRatingUpdater)
    {
        $this->averageRatingUpdater = $averageRatingUpdater;
    }

    /**
     * @param GenericEvent $event
     */
    public function recalculateSubjectRating(GenericEvent $event)
    {
        $subject = $event->getSubject();
        if (!$subject instanceof ReviewInterface) {
            throw new UnexpectedTypeException($subject, ReviewInterface::class);
        }

        if (ReviewInterface::STATUS_ACCEPTED === $subject->getStatus()) {
            $this->averageRatingUpdater->update($subject->getReviewSubject());
        }
    }
}
