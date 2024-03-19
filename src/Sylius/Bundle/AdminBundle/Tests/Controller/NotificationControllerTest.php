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

namespace Sylius\Bundle\AdminBundle\Tests\Controller;

use GuzzleHttp\Exception\ConnectException;
use Http\Message\MessageFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\ProphecyInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Sylius\Bundle\AdminBundle\Controller\NotificationController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NotificationControllerTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $client;

    private ObjectProphecy $legacyClient;

    private ObjectProphecy $requestFactory;

    private ObjectProphecy $messageFactory;

    private ObjectProphecy $streamFactory;

    private NotificationController $controller;

    private NotificationController $legacyController;

    private static string $hubUri = 'www.doesnotexist.test.com';

    /** @test */
    public function it_returns_an_empty_json_response_upon_client_exception(): void
    {
        $requestInterface = $this->prophesize(RequestInterface::class);
        $streamInterface = $this->prophesize(StreamInterface::class);

        $this->streamFactory->createStream(Argument::cetera())->willReturn($streamInterface);

        $this->requestFactory->createRequest(Argument::cetera())->willReturn($requestInterface);
        $requestInterface->withHeader('Content-Type', 'application/json')->willReturn($requestInterface);
        $requestInterface->withBody($streamInterface)->willReturn($requestInterface);

        $this->client->sendRequest(Argument::cetera())->willThrow(ConnectException::class);

        $emptyResponse = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $emptyResponse->getStatusCode());
        $this->assertEquals('""', $emptyResponse->getContent());
    }

    /**
     * @test
     *
     * @legacy This test will be removed in Sylius 2.0.
     */
    public function it_returns_an_empty_json_response_upon_client_exception_deprecated(): void
    {
        $requestInterface = $this->prophesize(RequestInterface::class);

        $this->messageFactory->createRequest(Argument::cetera())->willReturn($requestInterface);
        $requestInterface->withHeader('Content-Type', 'application/json')->willReturn($requestInterface);
        $requestInterface->withBody(Argument::any())->willReturn($requestInterface);

        $this->legacyClient->send(Argument::cetera())->willThrow(ConnectException::class);

        $emptyResponse = $this->legacyController->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $emptyResponse->getStatusCode());
        $this->assertEquals('""', $emptyResponse->getContent());
    }

    /** @test */
    public function it_returns_json_response_from_client_on_success(): void
    {
        $content = json_encode(['version' => '9001']);

        $requestInterface = $this->prophesize(RequestInterface::class);
        $streamInterface = $this->prophesize(StreamInterface::class);

        $this->streamFactory->createStream(Argument::cetera())->willReturn($streamInterface);

        $this->requestFactory->createRequest(Argument::cetera())->willReturn($requestInterface);
        $requestInterface->withHeader('Content-Type', 'application/json')->willReturn($requestInterface);
        $requestInterface->withBody($streamInterface)->willReturn($requestInterface);

        /** @var ProphecyInterface|StreamInterface $stream */
        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn($content);

        /** @var ProphecyInterface|ResponseInterface $externalResponse */
        $externalResponse = $this->prophesize(ResponseInterface::class);
        $externalResponse->getBody()->willReturn($stream->reveal());

        $this->client->sendRequest(Argument::cetera())->willReturn($externalResponse->reveal());

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($content, $response->getContent());
    }

    /**
     * @test
     *
     * @legacy This test will be removed in Sylius 2.0.
     */
    public function it_returns_json_response_from_client_on_success_deprecated(): void
    {
        $content = json_encode(['version' => '9001']);

        $requestInterface = $this->prophesize(RequestInterface::class);

        $this->messageFactory->createRequest(Argument::cetera())->willReturn($requestInterface);
        $requestInterface->withHeader('Content-Type', 'application/json')->willReturn($requestInterface);
        $requestInterface->withBody(Argument::any())->willReturn($requestInterface);

        /** @var ProphecyInterface|StreamInterface $stream */
        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn($content);

        /** @var ProphecyInterface|ResponseInterface $externalResponse */
        $externalResponse = $this->prophesize(ResponseInterface::class);
        $externalResponse->getBody()->willReturn($stream->reveal());

        $this->legacyClient->send(Argument::cetera())->willReturn($externalResponse->reveal());

        $response = $this->legacyController->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($content, $response->getContent());
    }

    protected function setUp(): void
    {
        $this->client = $this->prophesize(\Psr\Http\Client\ClientInterface::class);
        $this->legacyClient = $this->prophesize(\GuzzleHttp\ClientInterface::class);
        $this->requestFactory = $this->prophesize(RequestFactoryInterface::class);
        $this->messageFactory = $this->prophesize(MessageFactory::class);
        $this->streamFactory = $this->prophesize(StreamFactoryInterface::class);

        $this->controller = new NotificationController(
            $this->client->reveal(),
            $this->requestFactory->reveal(),
            self::$hubUri,
            'environment',
            $this->streamFactory->reveal(),
        );

        $this->legacyController = new NotificationController(
            $this->legacyClient->reveal(),
            $this->messageFactory->reveal(),
            self::$hubUri,
            'environment',
        );

        parent::setUp();
    }
}
