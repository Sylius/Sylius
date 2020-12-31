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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ErrorListener;
use Symfony\Component\HttpKernel\EventListener\ErrorListener as SymfonyErrorListener;

/**
 * @internal
 *
 * @see \Symfony\Component\HttpKernel\EventListener\ErrorListener
 */
final class CircularDependencyBreakingErrorListener extends ErrorListener
{
    /** @var CircularDependencyBreakingErrorListener */
    private $decoratedListener;

    public function __construct(SymfonyErrorListener $decoratedListener)
    {
        $this->decoratedListener = $decoratedListener;
    }

    public function logKernelException(ExceptionEvent $event): void
    {
        $this->decoratedListener->logKernelException($event);
    }

    public function onKernelException(ExceptionEvent $event, string $eventName = null, EventDispatcherInterface $eventDispatcher = null): void
    {
        try {
            $this->decoratedListener->onKernelException($event, $eventName, $eventDispatcher);
        } catch (\Throwable $throwable) {
            $this->breakCircularDependency($throwable);

            throw $throwable;
        }
    }

    public function onControllerArguments(ControllerArgumentsEvent $event): void
    {
        $this->decoratedListener->onControllerArguments($event);
    }

    private function breakCircularDependency(\Throwable $throwable): void
    {
        $throwables = [];

        do {
            $throwables[] = $throwable;

            if (in_array($throwable->getPrevious(), $throwables, true)) {
                $this->removePreviousFromThrowable($throwable);
            }

            $throwable = $throwable->getPrevious();
        } while (null !== $throwable);
    }

    private function removePreviousFromThrowable(\Throwable $throwable): void
    {
        $previous = new \ReflectionProperty($throwable instanceof \Exception ? \Exception::class : \Error::class, 'previous');
        $previous->setAccessible(true);
        $previous->setValue($throwable, null);
    }
}
