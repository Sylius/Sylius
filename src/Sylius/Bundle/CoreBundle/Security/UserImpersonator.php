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
    private SessionInterface $session;

    private string $sessionTokenParameter;

    private string $firewallContextName;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(SessionInterface $session, string $firewallContextName, EventDispatcherInterface $eventDispatcher)
    {
        $this->session = $session;
        $this->sessionTokenParameter = sprintf('_security_%s', $firewallContextName);
        $this->firewallContextName = $firewallContextName;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function impersonate(UserInterface $user): void
    {
        $token = new UsernamePasswordToken(
            $user,
            $this->firewallContextName,
            array_map(/** @param object|string $role */ static function ($role): string { return (string) $role; }, $user->getRoles())
        );
        $this->session->set($this->sessionTokenParameter, serialize($token));
        $this->session->save();

        $this->eventDispatcher->dispatch(new UserEvent($user), UserEvents::SECURITY_IMPERSONATE);
    }
}
