<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Tests\Listener;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\CircularDependencyBreakingExceptionListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class CircularDependencyBreakingExceptionListenerTest extends TestCase
{
    /** @test */
    public function it_breaks_circular_dependencies_in_exceptions(): void
    {
        // Arrange
        $decoratedListener = $this->createMock(ExceptionListener::class);
        $listener = new CircularDependencyBreakingExceptionListener($decoratedListener);

        $event = $this->createExceptionEvent();

        $secondException = new \Exception('Second');
        $firstException = new \Exception('First', 0, $secondException);
        $this->setPreviousForException($secondException, $firstException);

        $decoratedListener->method('onKernelException')->willThrowException($firstException);

        // Pre-assert
        Assert::assertSame($secondException, $firstException->getPrevious());
        Assert::assertSame($firstException, $secondException->getPrevious());

        // Act
        $throwable = null;

        try {
            $listener->onKernelException($event);
        } catch (\Throwable $throwable) {
        }

        // Assert
        Assert::assertNotNull($throwable);
        Assert::assertSame($firstException, $throwable);
        Assert::assertSame($secondException, $firstException->getPrevious());
        Assert::assertSame(null, $secondException->getPrevious());
    }

    /** @test */
    public function it_breaks_more_complex_circular_dependencies_in_exceptions(): void
    {
        // Arrange
        $decoratedListener = $this->createMock(ExceptionListener::class);
        $listener = new CircularDependencyBreakingExceptionListener($decoratedListener);

        $event = $this->createExceptionEvent();

        $fourthException = new \Exception('Fourth');
        $thirdException = new \Exception('Third', 0, $fourthException);
        $secondException = new \Exception('Second', 0, $thirdException);
        $firstException = new \Exception('First', 0, $secondException);
        $this->setPreviousForException($fourthException, $secondException);

        $decoratedListener->method('onKernelException')->willThrowException($firstException);

        // Pre-assert
        Assert::assertSame($secondException, $firstException->getPrevious());
        Assert::assertSame($thirdException, $secondException->getPrevious());
        Assert::assertSame($fourthException, $thirdException->getPrevious());
        Assert::assertSame($secondException, $fourthException->getPrevious());

        // Act
        $throwable = null;

        try {
            $listener->onKernelException($event);
        } catch (\Throwable $throwable) {
        }

        // Assert
        Assert::assertNotNull($throwable);
        Assert::assertSame($firstException, $throwable);
        Assert::assertSame($secondException, $firstException->getPrevious());
        Assert::assertSame($thirdException, $secondException->getPrevious());
        Assert::assertSame($fourthException, $thirdException->getPrevious());
        Assert::assertSame(null, $fourthException->getPrevious());
    }

    /** @test */
    public function it_does_nothing_when_circular_dependencies_are_not_found(): void
    {
        // Arrange
        $decoratedListener = $this->createMock(ExceptionListener::class);
        $listener = new CircularDependencyBreakingExceptionListener($decoratedListener);

        $event = $this->createExceptionEvent();

        $secondException = new \Exception('Second');
        $firstException = new \Exception('First', 0, $secondException);

        $decoratedListener->method('onKernelException')->willThrowException($firstException);

        // Pre-assert
        Assert::assertSame($secondException, $firstException->getPrevious());
        Assert::assertSame(null, $secondException->getPrevious());

        // Act
        $throwable = null;

        try {
            $listener->onKernelException($event);
        } catch (\Throwable $throwable) {
        }

        // Assert
        Assert::assertNotNull($throwable);
        Assert::assertSame($firstException, $throwable);
        Assert::assertSame($secondException, $firstException->getPrevious());
        Assert::assertSame(null, $secondException->getPrevious());
    }

    private function setPreviousForException(\Exception $exception, ?\Exception $previous): void
    {
        $property = new \ReflectionProperty(\Exception::class, 'previous');
        $property->setAccessible(true);
        $property->setValue($exception, $previous);
    }

    private function createExceptionEvent(): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $request = $this->createMock(Request::class);
        $exception = new \Exception();

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, $exception);
    }
}
