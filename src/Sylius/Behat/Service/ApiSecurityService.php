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

final class ApiSecurityService implements SecurityServiceInterface
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

    public function logIn(UserInterface $user): void
    {
        $this->client->request(
            'POST',
            '/new-api/admin-user-authentication-token',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode(['email' => $user->getEmail(), 'password' => 'sylius'])
        );

        $token = json_decode($this->client->getResponse()->getContent(), true)['token'];

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
