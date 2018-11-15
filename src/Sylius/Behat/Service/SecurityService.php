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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class SecurityService implements SecurityServiceInterface
{
    /** @var SessionInterface */
    private $session;

    /** @var CookieSetterInterface */
    private $cookieSetter;

    /** @var string */
    private $sessionTokenVariable;

    /**
     * @param string $firewallContextName
     */
    public function __construct(SessionInterface $session, CookieSetterInterface $cookieSetter, $firewallContextName)
    {
        $this->session = $session;
        $this->cookieSetter = $cookieSetter;
        $this->sessionTokenVariable = sprintf('_security_%s', $firewallContextName);
    }

    /**
     * {@inheritdoc}
     */
    public function logIn(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'randomstringbutnotnull', $user->getRoles());
        $this->setToken($token);
    }

    public function logOut()
    {
        $this->session->set($this->sessionTokenVariable, null);
        $this->session->save();

        $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentToken()
    {
        $serializedToken = $this->session->get($this->sessionTokenVariable);

        if (null === $serializedToken) {
            throw new TokenNotFoundException();
        }

        return unserialize($serializedToken);
    }

    /**
     * {@inheritdoc}
     */
    public function restoreToken(TokenInterface $token)
    {
        $this->setToken($token);
    }

    private function setToken(TokenInterface $token)
    {
        $serializedToken = serialize($token);
        $this->session->set($this->sessionTokenVariable, $serializedToken);
        $this->session->save();
        $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());
    }
}
