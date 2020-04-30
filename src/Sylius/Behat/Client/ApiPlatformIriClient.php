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

namespace Sylius\Behat\Client;

use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

final class ApiPlatformIriClient implements ApiIriClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(AbstractBrowser $client, SharedStorageInterface $sharedStorage)
    {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    public function showByIri(string $iri): Response
    {
        return $this->request(Request::custom($iri, 'GET', $this->sharedStorage->get('token')));
    }

    private function request(RequestInterface $request): Response
    {
        $this->client->request(
            $request->method(),
            $request->url(),
            $request->parameters(),
            $request->files(),
            $request->headers(),
            $request->content() ?? null
        );

        return $this->client->getResponse();
    }
}
