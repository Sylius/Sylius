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

namespace Tests\Sylius\Bundle\CoreBundle\Remover;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemover;
use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemoverInterface;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewerReviewsRemoverTest extends TestCase
{
    private MockObject&RepositoryInterface $reviewRepository;

    private MockObject&ObjectManager $reviewManager;

    private MockObject&ReviewableRatingUpdaterInterface $averageRatingUpdater;

    private ReviewerReviewsRemover $remover;

    protected function setUp(): void
    {
        $this->reviewRepository = $this->createMock(RepositoryInterface::class);
        $this->reviewManager = $this->createMock(ObjectManager::class);
        $this->averageRatingUpdater = $this->createMock(ReviewableRatingUpdaterInterface::class);

        $this->remover = new ReviewerReviewsRemover(
            $this->reviewRepository,
            $this->reviewManager,
            $this->averageRatingUpdater,
        );
    }

    public function testImplementsReviewerReviewsRemoverInterface(): void
    {
        $this->assertInstanceOf(ReviewerReviewsRemoverInterface::class, $this->remover);
    }

    public function testRemovesReviewerReviewsAndRecalculatesRatings(): void
    {
        $author = $this->createMock(ReviewerInterface::class);
        $reviewSubject = $this->createMock(ReviewableInterface::class);
        $review = $this->createMock(ReviewInterface::class);

        $review->method('getReviewSubject')->willReturn($reviewSubject);

        $this->reviewRepository
            ->expects($this->once())
            ->method('findBy')
            ->with(['author' => $author])
            ->willReturn([$review])
        ;

        $this->reviewManager->expects($this->once())->method('remove')->with($review);
        $this->reviewManager->expects($this->once())->method('flush');
        $this->averageRatingUpdater->expects($this->once())->method('update')->with($reviewSubject);

        $this->remover->removeReviewerReviews($author);
    }
}
