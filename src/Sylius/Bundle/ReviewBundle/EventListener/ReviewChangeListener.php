<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ReviewBundle\EventListener;

use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewChangeListener
{
    public function __construct(private ReviewableRatingUpdaterInterface $averageRatingUpdater)
    {
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->recalculateSubjectRating($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->recalculateSubjectRating($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $this->recalculateSubjectRating($args);
    }

    public function recalculateSubjectRating(LifecycleEventArgs $args): void
    {
        $subject = $args->getObject();

        if (!$subject instanceof ReviewInterface) {
            return;
        }

        $reviewSubject = $subject->getReviewSubject();

        if ($args instanceof PostRemoveEventArgs) {
            $reviewSubject->removeReview($subject);
        }

        $this->averageRatingUpdater->update($reviewSubject);
    }
}
