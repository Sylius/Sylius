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

namespace Sylius\Bundle\ApiBundle\EventListener;

use Doctrine\DBAL\Exception\DriverException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class PostgreSQLDriverExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof DriverException && $exception->getSQLState() === '22P02') {
            $event
                ->setResponse(
                    new JSONResponse(
                        ['message' => 'Invalid URL parameter for type integer'],
                        $event->getRequest()->getMethod() === Request::METHOD_GET ? Response::HTTP_NOT_FOUND : Response::HTTP_UNPROCESSABLE_ENTITY,
                    ),
                )
            ;
        }
    }
}
