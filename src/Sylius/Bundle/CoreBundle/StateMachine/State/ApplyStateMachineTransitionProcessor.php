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

namespace Sylius\Bundle\CoreBundle\StateMachine\State;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Resource\Context\Context;
use Sylius\Resource\Metadata\Operation;
use Sylius\Resource\Metadata\StateMachineAwareOperationInterface;
use Sylius\Resource\State\ProcessorInterface;
use Webmozart\Assert\Assert;

final class ApplyStateMachineTransitionProcessor implements ProcessorInterface
{
    public function __construct(
        private StateMachineInterface $stateMachine,
        private ProcessorInterface $writeProcessor,
    ) {
    }

    public function process(mixed $data, Operation $operation, Context $context): mixed
    {
        Assert::isInstanceOf($operation, StateMachineAwareOperationInterface::class);

        $transition = $operation->getStateMachineTransition() ?? null;
        $graph = $operation->getStateMachineGraph() ?? null;

        Assert::notNull($transition, sprintf('No State machine transition was found on operation "%s".', $operation->getName() ?? ''));
        Assert::notNull($graph, sprintf('No State machine graph was found on operation "%s".', $operation->getName() ?? ''));

        if (\is_object($data) && $this->stateMachine->can($data, $graph, $transition)) {
            $this->stateMachine->apply($data, $graph, $transition);
        }

        return $this->writeProcessor->process($data, $operation, $context);
    }
}
