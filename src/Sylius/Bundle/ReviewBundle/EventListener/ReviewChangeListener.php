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

namespace Sylius\Bundle\ReviewBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewInterface;

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
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->recalculateSubjectRating($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->recalculateSubjectRating($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->recalculateSubjectRating($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function recalculateSubjectRating(LifecycleEventArgs $args): void
    {
        $subject = $args->getObject();

        if (!$subject instanceof ReviewInterface) {
            return;
        }

        $this->averageRatingUpdater->update($subject->getReviewSubject());
    }
}
