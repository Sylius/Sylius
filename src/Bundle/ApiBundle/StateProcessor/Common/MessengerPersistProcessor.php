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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Common;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\Messenger\Exception\DelayedMessageHandlingException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

/** @implements ProcessorInterface<object, mixed> */
final readonly class MessengerPersistProcessor implements ProcessorInterface
{
    /** @param ProcessorInterface<object, mixed> $decoratedProcessor */
    public function __construct(private ProcessorInterface $decoratedProcessor)
    {
    }

    /** @throws \Throwable */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        try {
            return $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
        } catch (DelayedMessageHandlingException|HandlerFailedException $e) {
            while ($e instanceof DelayedMessageHandlingException) {
                /** @var \Throwable $e */
                $e = $e->getPrevious();
            }
            while ($e instanceof HandlerFailedException) {
                /** @var \Throwable $e */
                $e = $e->getPrevious();
            }

            throw $e;
        }
    }
}
