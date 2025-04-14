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
    /** @var AverageRatingCalculator */
    private AverageRatingCalculator $averageRatingCalculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->averageRatingCalculator = new AverageRatingCalculator();
    }

    public function testItImplementsReviewableRatingCalculatorInterface(): void
    {
        self::assertInstanceOf(
            ReviewableRatingCalculatorInterface::class,
            $this->averageRatingCalculator
        );
    }

    public function testCalculatingAverageRating(): void
    {
        /** @var ReviewableInterface|MockObject $reviewableMock */
        $reviewableMock = $this->createMock(ReviewableInterface::class);

        /** @var ReviewInterface|MockObject $review1Mock */
        $review1Mock = $this->createMock(ReviewInterface::class);

        /** @var ReviewInterface|MockObject $review2Mock */
        $review2Mock = $this->createMock(ReviewInterface::class);

        $reviewableMock->expects($this->once())->method('getReviews')->willReturn(new ArrayCollection([$review1Mock, $review2Mock]));

        $review1Mock->expects($this->once())->method('getStatus')->willReturn(ReviewInterface::STATUS_ACCEPTED);
        $review1Mock->expects($this->once())->method('getRating')->willReturn(4);

        $review2Mock->expects($this->once())->method('getStatus')->willReturn(ReviewInterface::STATUS_ACCEPTED);
        $review2Mock->expects($this->once())->method('getRating')->willReturn(5);

        self::assertSame(4.5, $this->averageRatingCalculator->calculate($reviewableMock));
    }

    public function testReturningZeroIfGivenReviewableObjectHasNoReviews(): void
    {
        /** @var ReviewableInterface|MockObject $reviewableMock */
        $reviewableMock = $this->createMock(ReviewableInterface::class);

        $reviewableMock->expects($this->once())->method('getReviews')->willReturn(new ArrayCollection([]));

        self::assertSame(0.0, $this->averageRatingCalculator->calculate($reviewableMock));
    }

    public function testReturningZeroIfGivenReviewableObjectHasReviewsButNoneOfThemIsAccepted(): void
    {
        /** @var ReviewableInterface|MockObject $reviewableMock */
        $reviewableMock = $this->createMock(ReviewableInterface::class);

        /** @var ReviewInterface|MockObject $reviewMock */
        $reviewMock = $this->createMock(ReviewInterface::class);

        $reviewableMock->expects($this->once())->method('getReviews')->willReturn(new ArrayCollection([$reviewMock]));

        $reviewMock->expects($this->once())->method('getStatus')->willReturn(ReviewInterface::STATUS_NEW);

        self::assertSame(0.0, $this->averageRatingCalculator->calculate($reviewableMock));
    }
}
