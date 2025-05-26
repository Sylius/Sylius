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

namespace Tests\Sylius\Component\Review\Calculator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Review\Calculator\AverageRatingCalculator;
use Sylius\Component\Review\Calculator\ReviewableRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class AverageRatingCalculatorTest extends TestCase
{
    private AverageRatingCalculator $averageRatingCalculator;

    /** @var ReviewableInterface&MockObject */
    private ReviewableInterface $reviewable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->averageRatingCalculator = new AverageRatingCalculator();
        $this->reviewable = $this->createMock(ReviewableInterface::class);
    }

    public function testShouldImplementReviewableRatingCalculatorInterface(): void
    {
        self::assertInstanceOf(ReviewableRatingCalculatorInterface::class, $this->averageRatingCalculator);
    }

    public function testCalculateAverageRating(): void
    {
        /** @var ReviewInterface&MockObject $review1 */
        $review1 = $this->createMock(ReviewInterface::class);

        /** @var ReviewInterface&MockObject $review2 */
        $review2 = $this->createMock(ReviewInterface::class);

        $this->reviewable->expects($this->once())->method('getReviews')->willReturn(new ArrayCollection([$review1, $review2]));

        $review1->expects($this->once())->method('getStatus')->willReturn(ReviewInterface::STATUS_ACCEPTED);
        $review1->expects($this->once())->method('getRating')->willReturn(4);

        $review2->expects($this->once())->method('getStatus')->willReturn(ReviewInterface::STATUS_ACCEPTED);
        $review2->expects($this->once())->method('getRating')->willReturn(5);

        self::assertSame(4.5, $this->averageRatingCalculator->calculate($this->reviewable));
    }

    public function testReturningZeroIfGivenReviewableObjectHasNoReviews(): void
    {
        $this->reviewable->expects($this->once())->method('getReviews')->willReturn(new ArrayCollection([]));
        self::assertSame(0.0, $this->averageRatingCalculator->calculate($this->reviewable));
    }

    public function testReturningZeroIfGivenReviewableObjectHasReviewsButNoneOfThemIsAccepted(): void
    {
        /** @var ReviewInterface&MockObject $review */
        $review = $this->createMock(ReviewInterface::class);

        $this->reviewable->expects($this->once())->method('getReviews')->willReturn(new ArrayCollection([$review]));

        $review->expects($this->once())->method('getStatus')->willReturn(ReviewInterface::STATUS_NEW);

        self::assertSame(0.0, $this->averageRatingCalculator->calculate($this->reviewable));
    }
}
