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

namespace spec\Sylius\Bundle\ApiBundle\Applicator;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachine as WinzouStateMachine;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Core\ProductReviewTransitions;
use Sylius\Component\Review\Model\ReviewInterface;

final class ProductReviewStateMachineTransitionApplicatorSpec extends ObjectBehavior
{
    function let(StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_accepts_product_review(
        StateMachineFactoryInterface $stateMachineFactory,
        ReviewInterface $review,
        WinzouStateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($review, ProductReviewTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(ProductReviewTransitions::TRANSITION_ACCEPT)->willReturn(true);
        $stateMachine->apply(ProductReviewTransitions::TRANSITION_ACCEPT)->shouldBeCalled();

        $this->accept($review);
    }

    function it_throws_exception_if_cannot_accept_product_review(
        StateMachineFactoryInterface $stateMachineFactory,
        ReviewInterface $review,
        WinzouStateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($review, ProductReviewTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(ProductReviewTransitions::TRANSITION_ACCEPT)->willReturn(false);
        $stateMachine->apply(ProductReviewTransitions::TRANSITION_ACCEPT)->shouldNotBeCalled();

        $this
            ->shouldThrow(StateMachineTransitionFailedException::class)
            ->during('accept', [$review])
        ;
    }

    function it_uses_the_new_state_machine_abstraction_if_passed_while_accepting_a_product_review(
        StateMachineInterface $stateMachine,
        ReviewInterface $review,
    ): void {
        $this->beConstructedWith($stateMachine);

        $stateMachine->can($review, ProductReviewTransitions::GRAPH, ProductReviewTransitions::TRANSITION_ACCEPT)->willReturn(true);
        $stateMachine->apply($review, ProductReviewTransitions::GRAPH, ProductReviewTransitions::TRANSITION_ACCEPT)->shouldBeCalled();

        $this->accept($review);
    }

    function it_throws_exception_if_cannot_accept_product_review_with_new_state_machine_abstraction(
        StateMachineInterface $stateMachine,
        ReviewInterface $review,
    ): void {
        $this->beConstructedWith($stateMachine);

        $stateMachine->can($review, ProductReviewTransitions::GRAPH, ProductReviewTransitions::TRANSITION_ACCEPT)->willReturn(false);
        $stateMachine->apply($review, ProductReviewTransitions::GRAPH, ProductReviewTransitions::TRANSITION_ACCEPT)->shouldNotBeCalled();

        $this
            ->shouldThrow(StateMachineTransitionFailedException::class)
            ->during('accept', [$review])
        ;
    }

    function it_rejects_product_review(
        StateMachineFactoryInterface $stateMachineFactory,
        ReviewInterface $review,
        WinzouStateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($review, ProductReviewTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(ProductReviewTransitions::TRANSITION_REJECT)->willReturn(true);
        $stateMachine->apply(ProductReviewTransitions::TRANSITION_REJECT)->shouldBeCalled();

        $this->reject($review);
    }

    function it_throws_exception_if_cannot_reject_product_review(
        StateMachineFactoryInterface $stateMachineFactory,
        ReviewInterface $review,
        WinzouStateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($review, ProductReviewTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(ProductReviewTransitions::TRANSITION_REJECT)->willReturn(false);
        $stateMachine->apply(ProductReviewTransitions::TRANSITION_REJECT)->shouldNotBeCalled();

        $this
            ->shouldThrow(StateMachineTransitionFailedException::class)
            ->during('reject', [$review])
        ;
    }

    function it_uses_the_new_state_machine_abstraction_if_passed_while_rejecting_a_product_review(
        StateMachineInterface $stateMachine,
        ReviewInterface $review,
    ): void {
        $this->beConstructedWith($stateMachine);

        $stateMachine->can($review, ProductReviewTransitions::GRAPH, ProductReviewTransitions::TRANSITION_REJECT)->willReturn(true);
        $stateMachine->apply($review, ProductReviewTransitions::GRAPH, ProductReviewTransitions::TRANSITION_REJECT)->shouldBeCalled();

        $this->reject($review);
    }

    function it_throws_exception_if_cannot_reject_product_review_with_new_state_machine_abstraction(
        StateMachineInterface $stateMachine,
        ReviewInterface $review,
    ): void
    {
        $this->beConstructedWith($stateMachine);

        $stateMachine->can($review, ProductReviewTransitions::GRAPH, ProductReviewTransitions::TRANSITION_REJECT)->willReturn(false);
        $stateMachine->apply($review, ProductReviewTransitions::GRAPH, ProductReviewTransitions::TRANSITION_REJECT)->shouldNotBeCalled();

        $this
            ->shouldThrow(StateMachineTransitionFailedException::class)
            ->during('reject', [$review]);
    }
}
