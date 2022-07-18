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

namespace Sylius\Bundle\CoreBundle\Security;

use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserImpersonator implements UserImpersonatorInterface
{
    private string $sessionTokenParameter;

    private string $firewallContextName;

    public function __construct(
        private SessionInterface $session,
        string $firewallContextName,
        private EventDispatcherInterface $eventDispatcher,
    ) {
        $this->sessionTokenParameter = sprintf('_security_%s', $firewallContextName);
        $this->firewallContextName = $firewallContextName;
    }

    public function impersonate(UserInterface $user): void
    {
        /** @deprecated parameter credential was deprecated in Symfony 5.4, so in Sylius 1.11 too, in Sylius 2.0 providing 4 arguments will be prohibited. */
        if (3 === (new \ReflectionClass(UsernamePasswordToken::class))->getConstructor()->getNumberOfParameters()) {
            $token = new UsernamePasswordToken(
                $user,
                $this->firewallContextName,
                array_map(/** @param object|string $role */ static fn ($role): string => (string) $role, $user->getRoles()),
            );
        } else {
            $token = new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                $this->firewallContextName,
                array_map(/** @param object|string $role */ static fn ($role): string => (string) $role, $user->getRoles()),
            );
        }

        $this->session->set($this->sessionTokenParameter, serialize($token));
        $this->session->save();

        $this->eventDispatcher->dispatch(new UserEvent($user), UserEvents::SECURITY_IMPERSONATE);
    }
}
