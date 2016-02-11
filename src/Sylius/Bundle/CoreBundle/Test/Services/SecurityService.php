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
use Sylius\Bundle\CoreBundle\Test\Factory\UserFactoryInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SecurityService implements SecurityServiceInterface
{
    const DEFAULT_USER_FIRST_NAME = 'John';
    const DEFAULT_USER_LAST_NAME = 'Doe';
    const DEFAULT_USER_EMAIL = 'john.doe@example.com';
    const DEFAULT_USER_PASSWORD = 'password123';

    const DEFAULT_PROVIDER_KEY = 'main';

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var UserFactoryInterface
     */
    private $userFactory;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param UserRepositoryInterface $userRepository
     * @param UserFactoryInterface $userFactory
     * @param SessionInterface $session
     */
    public function __construct(
        UserRepositoryInterface $userRepository,
        UserFactoryInterface $userFactory,
        SessionInterface $session
    ) {
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function logIn($email, $providerKey, Session $minkSession)
    {
        $user = $this->userRepository->findOneBy(['username' => $email]);
        if (null === $user) {
            throw new \InvalidArgumentException(sprintf('There is no user with email %s', $email));
        }

        $this->logInUser($minkSession, $user, $providerKey);
    }

    /**
     * {@inheritdoc}
     */
    public function logInDefaultUser(Session $minkSession)
    {
        $user = $this->userRepository->findOneBy(['username' => self::DEFAULT_USER_EMAIL]);
        if (null === $user) {
            $user = $this->createDefaultUser();
            $this->userRepository->add($user);
        }

        $this->logInUser($minkSession, $user);

        return $user;
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

    /**
     * @return UserInterface
     */
    private function createDefaultUser()
    {
        return $this->userFactory->create(
            self::DEFAULT_USER_FIRST_NAME,
            self::DEFAULT_USER_LAST_NAME,
            self::DEFAULT_USER_EMAIL,
            self::DEFAULT_USER_PASSWORD
        );
    }
}
