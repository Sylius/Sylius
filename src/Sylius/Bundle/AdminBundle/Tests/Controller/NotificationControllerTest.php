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

namespace Sylius\Bundle\AdminBundle\Tests\Controller;

use GuzzleHttp\Exception\ConnectException;
use Http\Client\Exception\NetworkException;
use Http\Message\MessageFactory;
use Http\Message\RequestFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophecy\ProphecyInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
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

    private NotificationController $controller;

    private NotificationController $legacyController;

    private static string $hubUri = 'www.doesnotexist.test.com';

    /** @test */
    public function it_returns_an_empty_json_response_upon_client_exception(): void
    {
        $this->requestFactory->createRequest(Argument::cetera())
            ->willReturn($this->prophesize(RequestInterface::class)->reveal())
        ;

        $this->client->sendRequest(Argument::cetera())->willThrow(NetworkException::class);

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
        $this->messageFactory->createRequest(Argument::any(), Argument::cetera())
            ->willReturn($this->prophesize(RequestInterface::class)->reveal())
        ;

        $this->legacyClient->send(Argument::cetera())->willThrow(ConnectException::class);

        $emptyResponse = $this->legacyController->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $emptyResponse->getStatusCode());
        $this->assertEquals('""', $emptyResponse->getContent());
    }

    /** @test */
    public function it_returns_json_response_from_client_on_success(): void
    {
        $content = json_encode(['version' => '9001']);

        $this->requestFactory->createRequest(Argument::cetera())
            ->willReturn($this->prophesize(RequestInterface::class)->reveal())
        ;

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

        $this->messageFactory->createRequest(Argument::any(), Argument::cetera())
            ->willReturn($this->prophesize(RequestInterface::class)->reveal())
        ;

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
        $this->requestFactory = $this->prophesize(RequestFactory::class);
        $this->messageFactory = $this->prophesize(MessageFactory::class);

        $this->controller = new NotificationController(
            $this->client->reveal(),
            $this->requestFactory->reveal(),
            self::$hubUri,
            'environment',
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
