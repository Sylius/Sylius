<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Provider;

use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
abstract class AbstractUserProvider implements UserProviderInterface
{
    /**
     * @var string
     */
    protected $supportedUserClass = UserInterface::class;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * @var CanonicalizerInterface
     */
    protected $canonicalizer;

    /**
     * @param string $supportedUserClass FQCN
     * @param UserRepositoryInterface $userRepository
     * @param CanonicalizerInterface  $canonicalizer
     */
    public function __construct(
        $supportedUserClass,
        UserRepositoryInterface $userRepository,
        CanonicalizerInterface $canonicalizer
    ) {
        $this->supportedUserClass = $supportedUserClass;
        $this->userRepository = $userRepository;
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $username = $this->canonicalizer->canonicalize($username);
        $user = $this->findUser($username);

        if (null === $user) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $username)
            );
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        if (null === $reloadedUser = $this->userRepository->find($user->getId())) {
            throw new UsernameNotFoundException(
                sprintf('User with ID "%d" could not be refreshed.', $user->getId())
            );
        }

        return $reloadedUser;
    }

    /**
     * {@inheritdoc}
     */
    abstract protected function findUser($uniqueIdentifier);

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $this->supportedUserClass === $class || is_subclass_of($class, $this->supportedUserClass);
    }
}
