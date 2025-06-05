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

namespace Tests\Sylius\Bundle\ApiBundle\StateProcessor\Common;

use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\StateProcessor\Common\MessengerPersistProcessor;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\DelayedMessageHandlingException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class MessengerPersistProcessorTest extends TestCase
{
    private MockObject&ProcessorInterface $decoratedProcessor;

    private MessengerPersistProcessor $messengerPersistProcessor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedProcessor = $this->createMock(ProcessorInterface::class);
        $this->messengerPersistProcessor = new MessengerPersistProcessor($this->decoratedProcessor);
    }

    public function testImplementsProcessorInterface(): void
    {
        self::assertInstanceOf(ProcessorInterface::class, $this->messengerPersistProcessor);
    }

    public function testUnwrapsDelayedMessageHandlingException(): void
    {
        $command = new CompleteOrder('ThankYou', 'token');
        $envelope = new Envelope($command);
        $operation = new Post();

        $exception = new DelayedMessageHandlingException(
            [new \RuntimeException('Delayed message exception')],
            $envelope,
        );

        $this->decoratedProcessor
            ->expects(self::once())
            ->method('process')
            ->with($envelope, $operation, [], [])
            ->willThrowException($exception);

        self::expectException(\RuntimeException::class);

        self::expectExceptionMessage('Delayed message exception');

        $this->messengerPersistProcessor->process($envelope, $operation, [], []);
    }

    public function testUnwrapsHandlerFailedException(): void
    {
        $command = new CompleteOrder('ThankYou', 'token');
        $envelope = new Envelope($command);
        $operation = new Post();

        $exception = new HandlerFailedException(
            $envelope,
            [new \RuntimeException('Delayed message exception')],
        );

        $this->decoratedProcessor
            ->expects(self::once())
            ->method('process')
            ->with($envelope, $operation, [], [])
            ->willThrowException($exception);

        self::expectException(\RuntimeException::class);

        self::expectExceptionMessage('Delayed message exception');

        $this->messengerPersistProcessor->process($envelope, $operation, [], []);
    }
}
