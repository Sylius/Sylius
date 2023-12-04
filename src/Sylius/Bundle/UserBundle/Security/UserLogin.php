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

namespace Sylius\Bundle\UserBundle\Security;

use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Webmozart\Assert\Assert;

class UserLogin implements UserLoginInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserCheckerInterface $userChecker,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function login(UserInterface $user, ?string $firewallName = null): void
    {
        $firewallName = $firewallName ?? 'main';

        Assert::isInstanceOf($user, SymfonyUserInterface::class);
        $this->userChecker->checkPreAuth($user);
        $this->userChecker->checkPostAuth($user);

        $token = $this->createToken($user, $firewallName);
        if (null === $token->getUser() || [] === $token->getUser()->getRoles()) {
            throw new AuthenticationException('Unauthenticated token');
        }

        $this->tokenStorage->setToken($token);
        $this->eventDispatcher->dispatch(new UserEvent($user), UserEvents::SECURITY_IMPLICIT_LOGIN);
    }

    protected function createToken(UserInterface $user, string $firewallName): UsernamePasswordToken
    {
        Assert::isInstanceOf($user, SymfonyUserInterface::class);
        /** @deprecated parameter credential was deprecated in Symfony 5.4, so in Sylius 1.11 too, in Sylius 2.0 providing 4 arguments will be prohibited. */
        if (3 === (new \ReflectionClass(UsernamePasswordToken::class))->getConstructor()->getNumberOfParameters()) {
            return new UsernamePasswordToken(
                $user,
                $firewallName,
                array_map(/** @param object|string $role */ static function ($role): string { return (string) $role; }, $user->getRoles()),
            );
        }

        return new UsernamePasswordToken(
            $user,
            null, // @phpstan-ignore-line continue to support Sf < 6
            $firewallName, // @phpstan-ignore-line continue to support Sf < 6
            array_map(/** @param object|string $role */ static fn ($role): string => (string) $role, $user->getRoles()),
        );
    }
}
