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
use SM\StateMachine\StateMachine;
use Sylius\Component\Core\ProductReviewTransitions;
use Sylius\Component\Review\Model\ReviewInterface;

final class ProductReviewStateMachineTransitionApplicatorSpec extends ObjectBehavior
{
    function let(StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    public function it_accepts_product_review(
        StateMachineFactoryInterface $stateMachineFactory,
        ReviewInterface $review,
        StateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($review, ProductReviewTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(ProductReviewTransitions::TRANSITION_ACCEPT)->shouldBeCalled();

        $this->accept($review);
    }

    public function it_rejects_product_review(
        StateMachineFactoryInterface $stateMachineFactory,
        ReviewInterface $review,
        StateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($review, ProductReviewTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(ProductReviewTransitions::TRANSITION_REJECT)->shouldBeCalled();

        $this->reject($review);
    }
}
