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

use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SecurityService implements SecurityServiceInterface
{
    private string $sessionTokenVariable;

    private string $firewallContextName;

    /**
     * @param string $firewallContextName
     */
    public function __construct(
        private RequestStack $requestStack,
        private CookieSetterInterface $cookieSetter,
        $firewallContextName,
    ) {
        $this->sessionTokenVariable = sprintf('_security_%s', $firewallContextName);
        $this->firewallContextName = $firewallContextName;
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

    public function logOut(): void
    {
        $this->requestStack->getSession()->set($this->sessionTokenVariable, null);
        $this->requestStack->getSession()->save();

        $this->cookieSetter->setCookie($this->requestStack->getSession()->getName(), $this->requestStack->getSession()->getId());
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

    private function setToken(TokenInterface $token)
    {
        $serializedToken = serialize($token);
        $this->requestStack->getSession()->set($this->sessionTokenVariable, $serializedToken);
        $this->requestStack->getSession()->save();
        $this->cookieSetter->setCookie($this->requestStack->getSession()->getName(), $this->requestStack->getSession()->getId());
    }
}
