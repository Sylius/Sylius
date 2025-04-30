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

namespace Tests\Sylius\Component\Review\Model;

use DateTime;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Review\Model\Review;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewTest extends TestCase
{
    private ReviewInterface $review;

    protected function setUp(): void
    {
        parent::setUp();
        $this->review = new Review();
    }
    public function testItImplementsReviewInterface(): void
    {
        self::assertInstanceOf(ReviewInterface::class, $this->review);
    }

    public function testTitle(): void
    {
        $this->review->setTitle('review title');
        self::assertSame('review title', $this->review->getTitle());
    }

    public function testRating(): void
    {
        $this->review->setRating(5);
        self::assertSame(5, $this->review->getRating());
    }

    public function testComment(): void
    {
        $this->review->setComment('Lorem ipsum dolor');
        self::assertSame('Lorem ipsum dolor', $this->review->getComment());
    }

    public function testAuthor(): void
    {
        $author = $this->createMock(ReviewerInterface::class);
        $this->review->setAuthor($author);
        self::assertSame($author, $this->review->getAuthor());
    }

    public function testDefaultStatus(): void
    {
        self::assertSame(ReviewInterface::STATUS_NEW, $this->review->getStatus());
    }

    public function testReviewSubject(): void
    {
        $reviewSubject = $this->createMock(ReviewableInterface::class);
        $this->review->setReviewSubject($reviewSubject);
        self::assertSame($reviewSubject, $this->review->getReviewSubject());
    }

    public function testCreatedAt(): void
    {
        $createdAt = new DateTime();
        $this->review->setCreatedAt($createdAt);
        self::assertSame($createdAt, $this->review->getCreatedAt());
    }

    public function testUpdatedAt(): void
    {
        $updatedAt = new DateTime();
        $this->review->setUpdatedAt($updatedAt);
        self::assertSame($updatedAt, $this->review->getUpdatedAt());
    }
}
