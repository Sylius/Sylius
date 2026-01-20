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

namespace Tests\Sylius\Bundle\ApiBundle\Applicator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Applicator\ProductReviewStateMachineTransitionApplicator;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Core\ProductReviewTransitions;
use Sylius\Component\Review\Model\ReviewInterface;

final class ProductReviewStateMachineTransitionApplicatorTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private ProductReviewStateMachineTransitionApplicator $productReviewStateMachineTransitionApplicator;

    private MockObject&ReviewInterface $review;

    public function testAcceptsProductReview(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with(
                $this->review,
                ProductReviewTransitions::GRAPH,
                ProductReviewTransitions::TRANSITION_ACCEPT,
            )->willReturn(true);

        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with(
                $this->review,
                ProductReviewTransitions::GRAPH,
                ProductReviewTransitions::TRANSITION_ACCEPT,
            );

        $this->productReviewStateMachineTransitionApplicator->accept($this->review);
    }

    public function testThrowsExceptionIfCannotAcceptProductReview(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with(
                $this->review,
                ProductReviewTransitions::GRAPH,
                ProductReviewTransitions::TRANSITION_ACCEPT,
            )->willReturn(false);

        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with(
                $this->review,
                ProductReviewTransitions::GRAPH,
                ProductReviewTransitions::TRANSITION_ACCEPT,
            );

        self::expectException(StateMachineTransitionFailedException::class);

        $this->productReviewStateMachineTransitionApplicator->accept($this->review);
    }

    public function testRejectsProductReview(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with(
                $this->review,
                ProductReviewTransitions::GRAPH,
                ProductReviewTransitions::TRANSITION_REJECT,
            )->willReturn(true);

        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with(
                $this->review,
                ProductReviewTransitions::GRAPH,
                ProductReviewTransitions::TRANSITION_REJECT,
            );

        $this->productReviewStateMachineTransitionApplicator->reject($this->review);
    }

    public function testThrowsExceptionIfCannotRejectProductReview(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with(
                $this->review,
                ProductReviewTransitions::GRAPH,
                ProductReviewTransitions::TRANSITION_REJECT,
            )->willReturn(false);

        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with(
                $this->review,
                ProductReviewTransitions::GRAPH,
                ProductReviewTransitions::TRANSITION_REJECT,
            );

        self::expectException(StateMachineTransitionFailedException::class);

        $this->productReviewStateMachineTransitionApplicator->reject($this->review);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->productReviewStateMachineTransitionApplicator = new ProductReviewStateMachineTransitionApplicator(
            $this->stateMachine,
        );
        $this->review = $this->createMock(ReviewInterface::class);
    }
}
