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

namespace Tests\Sylius\Component\Review\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Review\Factory\ReviewFactory;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ReviewFactoryTest extends TestCase
{
    /** @var FactoryInterface<ReviewInterface>&MockObject */
    private FactoryInterface $factory;

    /** @var ReviewFactory<ReviewInterface> */
    private ReviewFactory $reviewFactory;

    /** @var ReviewableInterface&MockObject */
    private ReviewableInterface $subject;

    /** @var ReviewInterface&MockObject */
    private ReviewInterface $review;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(FactoryInterface::class);
        $this->reviewFactory = new ReviewFactory($this->factory);
        $this->subject = $this->createMock(ReviewableInterface::class);
        $this->review = $this->createMock(ReviewInterface::class);
    }

    public function testShouldImplementFactoryInterface(): void
    {
        self::assertInstanceOf(FactoryInterface::class, $this->reviewFactory);
    }

    public function testImplementReviewFactoryInterface(): void
    {
        self::assertInstanceOf(ReviewFactoryInterface::class, $this->reviewFactory);
    }

    public function testCreatesNewReview(): void
    {
        $this->factory->expects($this->once())->method('createNew')->willReturn($this->review);
        self::assertSame($this->review, $this->reviewFactory->createNew());
    }

    public function testCreatesReviewWithSubject(): void
    {
        $this->factory->expects($this->once())->method('createNew')->willReturn($this->review);

        $this->review->expects($this->once())->method('setReviewSubject')->with($this->subject);

        $result = $this->reviewFactory->createForSubject($this->subject);

        self::assertSame($this->review, $result);
    }

    public function testCreatesReviewWithSubjectAndReviewer(): void
    {
        /** @var ReviewerInterface&MockObject $reviewer */
        $reviewer = $this->createMock(ReviewerInterface::class);

        $this->factory->expects($this->once())->method('createNew')->willReturn($this->review);

        $this->review->expects($this->once())->method('setReviewSubject')->with($this->subject);

        $this->review->expects($this->once())->method('setAuthor')->with($reviewer);

        $result = $this->reviewFactory->createForSubjectWithReviewer($this->subject, $reviewer);

        self::assertSame($this->review, $result);
    }
}
