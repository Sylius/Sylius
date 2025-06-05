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

namespace Tests\Sylius\Bundle\ReviewBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ReviewBundle\EventListener\ReviewChangeListener;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewChangeListenerTest extends TestCase
{
    /** @var ReviewableRatingUpdaterInterface&MockObject */
    private MockObject $averageRatingUpdater;

    private ReviewChangeListener $reviewChangeListener;

    /** @var LifecycleEventArgs&MockObject */
    private LifecycleEventArgs $event;

    /** @var ReviewInterface&MockObject */
    private ReviewInterface $review;

    /** @var ReviewableInterface&MockObject */
    private ReviewableInterface $reviewSubject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->averageRatingUpdater = $this->createMock(ReviewableRatingUpdaterInterface::class);
        $this->reviewChangeListener = new ReviewChangeListener($this->averageRatingUpdater);
        $this->event = $this->createMock(LifecycleEventArgs::class);
        $this->review = $this->createMock(ReviewInterface::class);
        $this->reviewSubject = $this->createMock(ReviewableInterface::class);
    }

    public function testRecalculatesSubjectRatingOnAcceptedReviewDeletion(): void
    {
        $this->event->expects(self::once())->method('getObject')->willReturn($this->review);

        $this->review->expects(self::once())->method('getReviewSubject')->willReturn($this->reviewSubject);

        $this->averageRatingUpdater->expects(self::once())->method('update')->with($this->reviewSubject);

        $this->reviewChangeListener->recalculateSubjectRating($this->event);
    }

    public function testRemovesAReviewFromAReviewSubjectOnThePreRemoveEvent(): void
    {
        /** @var EntityManagerInterface&MockObject $entityManager */
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $preRemoveEventArgs = new PreRemoveEventArgs($this->review, $entityManager);

        $this->review->expects(self::once())->method('getReviewSubject')->willReturn($this->reviewSubject);

        $this->reviewSubject->expects(self::once())->method('removeReview')->with($this->review);

        $this->averageRatingUpdater->expects(self::once())->method('update')->with($this->reviewSubject);

        $this->reviewChangeListener->recalculateSubjectRating($preRemoveEventArgs);
    }

    public function testDoesNothingIfEventSubjectIsNotReviewObject(): void
    {
        $this->event->expects(self::once())->method('getObject')->willReturn('badObject');

        $this->averageRatingUpdater->expects(self::never())
            ->method('update')
            ->with(self::isInstanceOf(ReviewableInterface::class));

        $this->reviewChangeListener->recalculateSubjectRating($this->event);
    }
}
