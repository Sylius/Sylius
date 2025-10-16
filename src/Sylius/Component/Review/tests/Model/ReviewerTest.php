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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Review\Model\Reviewer;
use Sylius\Component\Review\Model\ReviewerInterface;

final class ReviewerTest extends TestCase
{
    private ReviewerInterface $reviewer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reviewer = new Reviewer();
    }

    public function testShouldImplementReviewerInterface(): void
    {
        self::assertInstanceOf(ReviewerInterface::class, $this->reviewer);
    }

    public function testEmailShouldBeMutable(): void
    {
        $this->reviewer->setEmail('john.doe@example.com');
        self::assertSame('john.doe@example.com', $this->reviewer->getEmail());
    }

    public function testNameShouldBeMutable(): void
    {
        $this->reviewer->setFirstName('John');
        self::assertSame('John', $this->reviewer->getFirstName());
    }

    public function testLastNameShouldBeMutable(): void
    {
        $this->reviewer->setLastName('Doe');
        self::assertSame('Doe', $this->reviewer->getLastName());
    }
}
