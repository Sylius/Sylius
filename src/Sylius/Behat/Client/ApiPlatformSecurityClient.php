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

use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\HttpFoundation\Response;

final class ApiPlatformSecurityClient implements ApiSecurityClientInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var array */
    private $request = [];

    /** @var string */
    private $section;

    public function __construct(AbstractBrowser $client, string $section)
    {
        $this->client = $client;
        $this->section = $section;
    }

    public function prepareLoginRequest(): void
    {
        $this->request['url'] = sprintf('/new-api/%s-user-authentication-token', $this->section);
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
}
