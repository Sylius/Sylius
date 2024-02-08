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

namespace Sylius\Behat\Service;

use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionFactoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SecurityService implements RememberMeAwareSecurityServiceInterface
{
    private string $sessionTokenVariable;

    public function __construct(
        private RequestStack $requestStack,
        private CookieSetterInterface $cookieSetter,
        private string $firewallContextName,
        private ?SessionFactoryInterface $sessionFactory = null,
    ) {
        $this->sessionTokenVariable = sprintf('_security_%s', $firewallContextName);
    }

    public function logIn(UserInterface $user): void
    {
        /** @deprecated parameter credential was deprecated in Symfony 5.4, so in Sylius 1.11 too, in Sylius 2.0 providing 4 arguments will be prohibited. */
        if (3 === (new \ReflectionClass(UsernamePasswordToken::class))->getConstructor()->getNumberOfParameters()) {
            $token = new UsernamePasswordToken($user, $this->firewallContextName, $user->getRoles());
        } else {
            $token = new UsernamePasswordToken($user, $user->getPassword(), $this->firewallContextName, $user->getRoles());
        }

        $this->setToken($token);
    }

    public function logInWithRememberMe(UserInterface $user): void
    {
        $token = new RememberMeToken($user, $this->firewallContextName, 'secret');

        $this->setToken($token);
    }

    public function logOut(): void
    {
        try {
            $this->setTokenCookie();
        } catch (SessionNotFoundException) {
        }
    }

    public function getCurrentToken(): TokenInterface
    {
        $serializedToken = $this->requestStack->getSession()->get($this->sessionTokenVariable);

        if (null === $serializedToken) {
            throw new TokenNotFoundException();
        }

        return unserialize($serializedToken);
    }

    public function restoreToken(TokenInterface $token): void
    {
        $this->setToken($token);
    }

    private function setToken(TokenInterface $token): void
    {
        if (null !== $this->sessionFactory) {
            $session = $this->sessionFactory->createSession();
            $request = new Request();
            $request->setSession($session);
            $this->requestStack->push($request);
        }

        $this->setTokenCookie(serialize($token));
    }

    private function setTokenCookie($serializedToken = null): void
    {
        $session = $this->requestStack->getSession();
        $session->set($this->sessionTokenVariable, $serializedToken);
        $session->save();
        $this->cookieSetter->setCookie($session->getName(), $session->getId());
    }
}
