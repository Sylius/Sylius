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

final class ApiPlatformSecurityClient implements ApiSecurityClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var string */
    private $section;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var array */
    private $request = [];

    public function __construct(AbstractBrowser $client, string $section, SharedStorageInterface $sharedStorage)
    {
        $this->client = $client;
        $this->section = $section;
        $this->sharedStorage = $sharedStorage;
    }

    public function prepareLoginRequest(): void
    {
        $this->request['url'] = sprintf('/api/v2/%s/authentication-token', $this->section);
        $this->request['method'] = 'POST';
    }

    public function setEmail(string $email): void
    {
        $this->request['body']['email'] = $email;
    }

    public function setPassword(string $password): void
    {
        $this->request['body']['password'] = $password;
    }

    public function call(): void
    {
        $this->client->request(
            $this->request['method'],
            $this->request['url'],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode($this->request['body'])
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        if (isset($content['token'])) {
            $this->sharedStorage->set('token', $content['token']);
        }
    }

    public function isLoggedIn(): bool
    {
        $response = $this->client->getResponse();

        return
            isset(json_decode($response->getContent(), true)['token']) &&
            $response->getStatusCode() !== Response::HTTP_UNAUTHORIZED
        ;
    }

    public function getErrorMessage(): string
    {
        return json_decode($this->client->getResponse()->getContent(), true)['message'];
    }

    public function logOut(): void
    {
        $this->sharedStorage->set('token', null);

        if ($this->sharedStorage->has('cart_token')) {
            $this->sharedStorage->set('previous_cart_token', $this->sharedStorage->get('cart_token'));
            $this->sharedStorage->set('cart_token', null);
        }
    }
}
