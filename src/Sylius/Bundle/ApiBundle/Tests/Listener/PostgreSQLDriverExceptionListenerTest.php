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

namespace Sylius\Bundle\ApiBundle\Tests\Listener;

use Doctrine\DBAL\Exception\DriverException;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\EventListener\PostgreSQLDriverExceptionListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class PostgreSQLDriverExceptionListenerTest extends TestCase
{
    /** @test */
    public function it_does_nothing_if_exception_is_not_a_driver_exception(): void
    {
        $event = $this->createExceptionEvent();

        (new PostgreSQLDriverExceptionListener())->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    /** @test */
    public function it_does_nothing_if_exception_is_a_driver_exception_but_sql_state_is_not_22P02(): void
    {
        $event = $this->createDriverExceptionEvent('some_other_sql_state', Request::METHOD_PATCH);

        (new PostgreSQLDriverExceptionListener())->onKernelException($event);

        $this->assertNull($event->getResponse());
    }

    /** @test */
    public function it_responses_with_not_found_status_if_exception_is_a_driver_exception_with_sql_state_22P02_and_request_method_is_get(): void
    {
        $event = $this->createDriverExceptionEvent('22P02', Request::METHOD_GET);

        (new PostgreSQLDriverExceptionListener())->onKernelException($event);

        $response = $event->getResponse();

        $this->assertNotNull($response);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals(json_encode(['message' => 'Invalid URL parameter for type integer']), $response->getContent());
    }

    /** @test */
    public function it_responses_with_unprocessable_entity_status_if_exception_is_a_driver_exception_with_sql_state_22P02_and_request_method_is_not_get(): void
    {
        $event = $this->createDriverExceptionEvent('22P02', Request::METHOD_PATCH);

        (new PostgreSQLDriverExceptionListener())->onKernelException($event);

        $response = $event->getResponse();

        $this->assertNotNull($response);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertEquals(json_encode(['message' => 'Invalid URL parameter for type integer']), $response->getContent());
    }

    private function createExceptionEvent(): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);
        $exception = new \Exception();

        return new ExceptionEvent($kernel, new Request(), HttpKernelInterface::MAIN_REQUEST, $exception);
    }

    private function createDriverExceptionEvent(string $sqlState, string $requestMethod): ExceptionEvent
    {
        $kernel = $this->createMock(HttpKernelInterface::class);

        $exception = $this->getMockBuilder(DriverException::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSQLState'])
            ->getMock();

        $request = new Request();
        $request->setMethod($requestMethod);

        $exception->method('getSQLState')->willReturn($sqlState);

        return new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);
    }
}
