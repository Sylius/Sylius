<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service;

use Sylius\Behat\Service\Setter\CookieSetterInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class SecurityService implements SecurityServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var CookieSetterInterface
     */
    private $cookieSetter;

    /**
     * @var string
     */
    private $sessionTokenVariable;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param SessionInterface $session
     * @param CookieSetterInterface $cookieSetter
     * @param string $contextKey
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        SessionInterface $session,
        CookieSetterInterface $cookieSetter,
        $contextKey
    ) {
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->cookieSetter = $cookieSetter;
        $this->sessionTokenVariable = sprintf('_security_%s', $contextKey);
    }

    /**
     * {@inheritdoc}
     */
    public function logIn($email)
    {
        /** @var UserInterface $user */
        $user = $this->userRepository->findOneBy(['username' => $email]);
        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('There is no user with email %s', $email));
        }

        $this->logUserIn($user);
    }

    public function logOut()
    {
        $this->setSerializedToken(null);

        $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function performActionAs(UserInterface $user, callable $action)
    {
        $previousToken = $this->getToken();
        $this->logUserIn($user);
        $action();

        if (null !== $previousToken) {
            $this->restorePreviousSessionToken($previousToken);

            return;
        }

        $this->logOut();
    }

    /**
     * @param UserInterface $user
     */
    private function logUserIn(UserInterface $user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'randomstringbutnotnull', $user->getRoles());
        $serializedToken = serialize($token);

        $this->setSerializedToken($serializedToken);

        $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());
    }

    /**
     * @param string $token
     */
    private function restorePreviousSessionToken($token)
    {
        $this->setSerializedToken($token);

        $this->cookieSetter->setCookie($this->session->getName(), $this->session->getId());
    }

    /**
     * @param string $token
     */
    private function setSerializedToken($token)
    {
        $this->session->set($this->sessionTokenVariable, $token);
        $this->session->save();
    }

    /**
     * @return string
     */
    private function getToken()
    {
        return $this->session->get($this->sessionTokenVariable);
    }
}
