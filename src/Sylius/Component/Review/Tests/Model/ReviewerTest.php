<?php

declare(strict_types=1);

namespace Tests\Sylius\Component\Review\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Review\Model\Reviewer;
use Sylius\Component\Review\Model\ReviewerInterface;

final class ReviewerTest extends TestCase
{
    private ReviewerInterface $reviewer;

    protected function setUp(): void
    {
        $this->reviewer = new Reviewer();
    }

    public function testItImplementsReviewerInterface(): void
    {
        self::assertInstanceOf(ReviewerInterface::class, $this->reviewer);
    }

    public function testHasAnEmail(): void
    {
        $this->reviewer->setEmail('john.doe@example.com');
        self::assertSame('john.doe@example.com', $this->reviewer->getEmail());
    }

    public function testHasAFirstName(): void
    {
        $this->reviewer->setFirstName('John');
        self::assertSame('John', $this->reviewer->getFirstName());
    }

    public function testHasALastName(): void
    {
        $this->reviewer->setLastName('Doe');
        self::assertSame('Doe', $this->reviewer->getLastName());
    }
}
