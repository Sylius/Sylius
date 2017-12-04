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

namespace Sylius\Bundle\AdminBundle\Controller;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use Http\Message\MessageFactory;
use Psr\Http\Message\UriInterface;
use Sylius\Bundle\CoreBundle\Application\Kernel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class NotificationController
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
     * @var UriInterface
     */
    private $hubUri;

    /**
     * @var string
     */
    private $environment;

    /**
     * @param ClientInterface $client
     * @param MessageFactory $messageFactory
     * @param string $hubUri
     * @param string $environment
     */
    public function __construct(
        ClientInterface $client,
        MessageFactory $messageFactory,
        string $hubUri,
        string $environment
    ) {
        $this->client = $client;
        $this->messageFactory = $messageFactory;
        $this->hubUri = new Uri($hubUri);
        $this->environment = $environment;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getVersionAction(Request $request): JsonResponse
    {
        $content = [
            'version' => Kernel::VERSION,
            'hostname' => $request->getHost(),
            'locale' => $request->getLocale(),
            'user_agent' => $request->headers->get('User-Agent'),
            'environment' => $this->environment,
        ];

        $headers = ['Content-Type' => 'application/json'];

        $hubRequest = $this->messageFactory->createRequest(
            Request::METHOD_GET,
            $this->hubUri,
            $headers,
            json_encode($content)
        );

        try {
            $hubResponse = $this->client->send($hubRequest, ['verify' => false]);
        } catch (GuzzleException $exception) {
            return JsonResponse::create('', JsonResponse::HTTP_NO_CONTENT);
        }

        $hubResponse = json_decode($hubResponse->getBody()->getContents(), true);

        return new JsonResponse($hubResponse);
    }
}
