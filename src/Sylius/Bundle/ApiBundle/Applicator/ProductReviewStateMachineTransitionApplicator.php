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

namespace Sylius\Bundle\ApiBundle\Applicator;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Abstraction\StateMachine\WinzouStateMachineAdapter;
use Sylius\Bundle\ApiBundle\Exception\ProductReviewAcceptanceFailedException;
use Sylius\Bundle\ApiBundle\Exception\ProductReviewRejectionFailedException;
use Sylius\Component\Core\ProductReviewTransitions;
use Sylius\Component\Review\Model\ReviewInterface;

/** @experimental */
final class ProductReviewStateMachineTransitionApplicator implements ProductReviewStateMachineTransitionApplicatorInterface
{
    public function __construct(private StateMachineFactoryInterface|StateMachineInterface $stateMachineFactory)
    {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            trigger_deprecation(
                'sylius/api-bundle',
                '1.13',
                sprintf(
                    'Passing an instance of "%s" as the first argument is deprecated. It will accept only instances of "%s" in Sylius 2.0.',
                    StateMachineFactoryInterface::class,
                    StateMachineInterface::class,
                ),
            );
        }
    }

    public function accept(ReviewInterface $data): ReviewInterface
    {
        try {
            $this->applyTransition($data, ProductReviewTransitions::TRANSITION_ACCEPT);
        } catch (StateMachineExecutionException) {
            throw new ProductReviewAcceptanceFailedException();
        }

        return $data;
    }

    public function reject(ReviewInterface $data): ReviewInterface
    {
        try {
            $this->applyTransition($data, ProductReviewTransitions::TRANSITION_REJECT);
        } catch (StateMachineExecutionException) {
            throw new ProductReviewRejectionFailedException();
        }
        $this->applyTransition($data, ProductReviewTransitions::TRANSITION_REJECT);

        return $data;
    }

    private function applyTransition(ReviewInterface $review, string $transition): void
    {
        $this->getStateMachine()->apply($review, ProductReviewTransitions::GRAPH, $transition);
    }

    private function getStateMachine(): StateMachineInterface
    {
        if ($this->stateMachineFactory instanceof StateMachineFactoryInterface) {
            return new WinzouStateMachineAdapter($this->stateMachineFactory);
        }

        return $this->stateMachineFactory;
    }
}
