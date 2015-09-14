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

use Sylius\Bundle\ReviewBundle\Updater\ReviewableAverageRatingUpdaterInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.p.zalewski@gmail.com>
 */
class ReviewDeleteListener
{
    /**
     * @var ReviewableAverageRatingUpdaterInterface
     */
    private $reviewableAverageRatingUpdater;

    /**
     * @param ReviewableAverageRatingUpdaterInterface $reviewableAverageRatingUpdater
     */
    public function __construct(ReviewableAverageRatingUpdaterInterface $reviewableAverageRatingUpdater)
    {
        $this->reviewableAverageRatingUpdater = $reviewableAverageRatingUpdater;
    }

    public function recalculateSubjectRating(GenericEvent $event)
    {
        if (!(($subject = $event->getSubject()) instanceof ReviewInterface)) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Review\Model\ReviewInterface');
        }

        if (ReviewInterface::STATUS_NEW === $subject->getStatus()
            || ReviewInterface::STATUS_REJECTED === $subject->getStatus()) {
            return;
        }

        $this->reviewableAverageRatingUpdater->update($subject);
    }
}
