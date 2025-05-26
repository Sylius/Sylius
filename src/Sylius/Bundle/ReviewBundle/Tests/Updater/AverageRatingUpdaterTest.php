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

namespace Tests\Sylius\Bundle\ReviewBundle\Updater;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ReviewBundle\Updater\AverageRatingUpdater;
use Sylius\Bundle\ReviewBundle\Updater\ReviewableRatingUpdaterInterface;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class AverageRatingUpdaterTest extends TestCase
{
    /** @var ReviewableRatingCalculatorInterface&MockObject */
    private MockObject $averageRatingCalculator;

    /** @var ObjectManager&MockObject */
    private MockObject $reviewSubjectManager;

    private AverageRatingUpdater $averageRatingUpdater;

    /** @var ReviewableInterface&MockObject */
    private ReviewableInterface $reviewSubject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->averageRatingCalculator = $this->createMock(ReviewableRatingCalculatorInterface::class);
        $this->reviewSubjectManager = $this->createMock(ObjectManager::class);
        $this->averageRatingUpdater = new AverageRatingUpdater(
            $this->averageRatingCalculator,
            $this->reviewSubjectManager,
        );
        $this->reviewSubject = $this->createMock(ReviewableInterface::class);
    }

    public function testImplementsProductAverageRatingUpdaterInterface(): void
    {
        self::assertInstanceOf(ReviewableRatingUpdaterInterface::class, $this->averageRatingUpdater);
    }

    public function testUpdatesReviewSubjectAverageRating(): void
    {
        $this->averageRatingCalculator->expects(self::once())
            ->method('calculate')
            ->with($this->reviewSubject)
            ->willReturn(4.5);

        $this->reviewSubject->expects(self::once())
            ->method('setAverageRating')
            ->with(4.5);

        $this->reviewSubjectManager->expects(self::once())->method('flush');

        $this->averageRatingUpdater->update($this->reviewSubject);
    }

    public function testUpdatesReviewSubjectAverageRatingFromReview(): void
    {
        /** @var ReviewInterface&MockObject $reviewMock */
        $reviewMock = $this->createMock(ReviewInterface::class);

        $reviewMock->expects(self::once())->method('getReviewSubject')->willReturn($this->reviewSubject);

        $this->averageRatingCalculator->expects(self::once())
            ->method('calculate')
            ->with($this->reviewSubject)
            ->willReturn(4.5);

        $this->reviewSubject->expects(self::once())->method('setAverageRating')->with(4.5);

        $this->reviewSubjectManager->expects(self::once())->method('flush');

        $this->averageRatingUpdater->updateFromReview($reviewMock);
    }
}
