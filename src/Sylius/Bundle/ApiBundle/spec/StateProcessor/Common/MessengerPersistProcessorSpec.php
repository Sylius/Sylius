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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Common;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\DelayedMessageHandlingException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class MessengerPersistProcessorSpec extends ObjectBehavior
{
    function let(ProcessorInterface $decoratedProcessor): void
    {
        $this->beConstructedWith($decoratedProcessor);
    }

    function it_implements_processor_interface(): void
    {
        $this->shouldImplement(ProcessorInterface::class);
    }

    function it_unwraps_delayed_message_handling_exception(ProcessorInterface $decoratedProcessor): void
    {
        $command = new CompleteOrder('ThankYou', 'token');
        $envelope = new Envelope($command);
        $operation = new Post();

        $exception = new DelayedMessageHandlingException([new \RuntimeException('Delayed message exception')], $envelope);

        $decoratedProcessor->process($envelope, $operation, [], [])->willThrow($exception);

        $this
            ->shouldThrow(new \RuntimeException('Delayed message exception'))
            ->during('process', [$envelope, $operation, [], []])
        ;
    }

    function it_unwraps_handler_failed_exception(ProcessorInterface $decoratedProcessor): void
    {
        $command = new CompleteOrder('ThankYou', 'token');
        $envelope = new Envelope($command);
        $operation = new Post();

        $decoratedProcessor->process($envelope, $operation, [], [])->willThrow(
            new HandlerFailedException(
                $envelope,
                [new \RuntimeException('Delayed message exception')],
            ),
        );

        $this
            ->shouldThrow(new \RuntimeException('Delayed message exception'))
            ->during('process', [$envelope, $operation, [], []])
        ;
    }
}
