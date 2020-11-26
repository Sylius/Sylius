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

namespace Sylius\Behat\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Webmozart\Assert\Assert;

final class ApiSecurityService implements SecurityServiceInterface
{
    /** @var AbstractBrowser */
    private $client;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var string */
    private $loginEndpoint;

    public function __construct(AbstractBrowser $client, SharedStorageInterface $sharedStorage, string $loginEndpoint)
    {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
        $this->loginEndpoint = $loginEndpoint;
    }

    public function logIn(UserInterface $user): void
    {
        error_reporting(error_reporting() & ~E_USER_DEPRECATED);

        $this->client->request(
            'POST',
            sprintf('/new-api/%s/authentication-token', $this->loginEndpoint),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => $user->getEmail(), 'password' => 'sylius'])
        );

        $response = $this->client->getResponse();
        $content = json_decode($response->getContent(), true);

        Assert::keyExists(
            $content,
            'token',
            SprintfResponseEscaper::provideMessageWithEscapedResponseContent('Token not found.', $response)
        );

        $token = $content['token'];

        $this->sharedStorage->set('token', $token);
    }

    public function logOut(): void
    {
        $this->sharedStorage->set('token', null);
    }

    public function getCurrentToken(): TokenInterface
    {
        $token = new JWTUserToken();
        $token->setRawToken($this->sharedStorage->get('token'));

        return $token;
    }

    public function restoreToken(TokenInterface $token): void
    {
        $this->sharedStorage->set('token', (string) $token);
    }
}
