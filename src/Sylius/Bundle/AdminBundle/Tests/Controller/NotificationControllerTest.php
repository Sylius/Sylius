<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\Tests\Controller;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ConnectException;
use Http\Message\MessageFactory;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Sylius\Bundle\AdminBundle\Controller\NotificationController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class NotificationControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var NotificationController
     */
    private $controller;

    /**
     * @var string
     */
    private static $hubUri = 'www.doesnotexist.test.com';

    /**
     * @test
     */
    public function it_returns_an_empty_json_response_upon_client_exception()
    {
        $this->messageFactory->createRequest(Argument::cetera())
            ->willReturn($this->prophesize(RequestInterface::class)->reveal())
        ;

        $this->client->send(Argument::cetera())->willThrow(ConnectException::class);

        $emptyResponse = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_NO_CONTENT, $emptyResponse->getStatusCode());
        $this->assertEquals('""', $emptyResponse->getContent());
    }

    /**
     * @test
     */
    public function it_returns_json_response_from_client_on_success()
    {
        $content = json_encode(['version' => '9001']);

        $this->messageFactory->createRequest(Argument::cetera())
            ->willReturn($this->prophesize(RequestInterface::class)->reveal())
        ;

        $stream = $this->prophesize(StreamInterface::class);
        $stream->getContents()->willReturn($content);

        $externalResponse = $this->prophesize(ResponseInterface::class);
        $externalResponse->getBody()->willReturn($stream->reveal());

        $this->client->send(Argument::cetera())->willReturn($externalResponse->reveal());

        $response = $this->controller->getVersionAction(new Request());

        $this->assertEquals(JsonResponse::HTTP_OK, $response->getStatusCode());
        $this->assertEquals($content, $response->getContent());
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->client = $this->prophesize(ClientInterface::class);
        $this->messageFactory = $this->prophesize(MessageFactory::class);

        $this->controller = new NotificationController(
            $this->client->reveal(),
            $this->messageFactory->reveal(),
            self::$hubUri
        );

        parent::setUp();
    }
}
