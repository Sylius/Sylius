<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Test\Services;

use Behat\Mink\Session;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SecurityService implements SecurityServiceInterface
{
    const DEFAULT_PROVIDER_KEY = 'main';

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param SessionInterface $session
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        SessionInterface $session
    ) {
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function logIn($email, $providerKey = self::DEFAULT_PROVIDER_KEY, Session $minkSession)
    {
        $user = $this->userRepository->findOneBy(['username' => $email]);
        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('There is no user with email %s', $email));
        }

        $this->logInUser($minkSession, $user, $providerKey);
    }

    /**
     * @param Session $minkSession
     * @param UserInterface $user
     * @param string $providerKey
     */
    private function logInUser(Session $minkSession, UserInterface $user, $providerKey = self::DEFAULT_PROVIDER_KEY)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $providerKey, $user->getRoles());

        $this->session->set('_security_user', serialize($token));
        $this->session->save();

        $minkSession->setCookie($this->session->getName(), $this->session->getId());
    }
}
