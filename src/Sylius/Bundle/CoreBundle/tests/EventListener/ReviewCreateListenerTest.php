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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\ReviewCreateListener;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ReviewCreateListenerTest extends TestCase
{
    private CustomerContextInterface&MockObject $customerContext;

    private ReviewCreateListener $reviewCreateListener;

    protected function setUp(): void
    {
        $this->customerContext = $this->createMock(CustomerContextInterface::class);
        $this->reviewCreateListener = new ReviewCreateListener($this->customerContext);
    }

    public function testAddsCurrentlyLoggedCustomerAsAuthorToNewlyCreatedReviewIfItHasNoAuthorYet(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $event = $this->createMock(GenericEvent::class);
        $review = $this->createMock(ReviewInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($review);
        $this->customerContext->expects($this->once())->method('getCustomer')->willReturn($customer);
        $review->expects($this->once())->method('getAuthor')->willReturn(null);
        $review->expects($this->once())->method('setAuthor')->with($customer);

        $this->reviewCreateListener->ensureReviewHasAuthor($event);
    }

    public function testThrowsExceptionIfEventObjectIsNotReviewWhileControllingAuthor(): void
    {
        $event = $this->createMock(GenericEvent::class);

        $event->expects($this->once())->method('getSubject')->willReturn('badObject');

        $this->expectException(InvalidArgumentException::class);

        $this->reviewCreateListener->ensureReviewHasAuthor($event);
    }

    public function testDoesNothingIfReviewAlreadyHasAuthor(): void
    {
        $existingAuthor = $this->createMock(CustomerInterface::class);
        $event = $this->createMock(GenericEvent::class);
        $review = $this->createMock(ReviewInterface::class);

        $event->expects($this->once())->method('getSubject')->willReturn($review);
        $review->expects($this->once())->method('getAuthor')->willReturn($existingAuthor);
        $this->customerContext->expects($this->never())->method('getCustomer');

        $this->reviewCreateListener->ensureReviewHasAuthor($event);
    }
}
