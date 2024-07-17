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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\DelayedMessageHandlingException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class MessengerDataPersisterSpec extends ObjectBehavior
{
    function let(ContextAwareDataPersisterInterface $decoratedDataPersister)
    {
        $this->beConstructedWith($decoratedDataPersister);
    }

    function it_calls_supports_method_from_decorated_data_presister(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
    ): void {
        $decoratedDataPersister->supports(Argument::any(), [])->shouldBeCalled();

        $this->supports(Argument::any(), []);
    }

    function it_unwraps_delayed_message_handling_exception(ContextAwareDataPersisterInterface $decoratedDataPersister): void
    {
        $completeOrder = new CompleteOrder('ThankYou');
        $completeOrder->setOrderTokenValue('ORDERTOKEN');
        $envelope = new Envelope($completeOrder);

        if (version_compare(Kernel::VERSION, '6.3.5', '>=')) {
            $exception = new DelayedMessageHandlingException([new \RuntimeException('Delayed message exception')], $envelope);
        } else {
            $exception = new DelayedMessageHandlingException([new \RuntimeException('Delayed message exception')]);
        }

        $decoratedDataPersister->persist($envelope, [])->willThrow($exception);

        $this->shouldThrow(new \RuntimeException('Delayed message exception'))->during('persist', [$envelope, []]);
    }

    function it_unwraps_handler_failed_exception(ContextAwareDataPersisterInterface $decoratedDataPersister): void
    {
        $completeOrder = new CompleteOrder('ThankYou');
        $completeOrder->setOrderTokenValue('ORDERTOKEN');
        $envelope = new Envelope($completeOrder);

        $decoratedDataPersister->persist($envelope, [])->willThrow(
            new HandlerFailedException(
                $envelope,
                [new \RuntimeException('Delayed message exception')],
            ),
        );

        $this->shouldThrow(new \RuntimeException('Delayed message exception'))->during('persist', [$envelope, []]);
    }

    function it_calls_remove_method_from_decorated_data_presister(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
    ): void {
        $decoratedDataPersister->remove(Argument::any(), [])->shouldBeCalled();

        $this->remove(Argument::any(), []);
    }
}
