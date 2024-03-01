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

namespace Sylius\Bundle\CoreBundle\Security;

use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface as SyliusUserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Webmozart\Assert\Assert;

final class UserImpersonator implements UserImpersonatorInterface
{
    private string $sessionTokenParameter;

    private string $firewallContextName;

    public function __construct(
        private RequestStack $requestStack,
        string $firewallContextName,
        private EventDispatcherInterface $eventDispatcher,
    ) {
        $this->sessionTokenParameter = sprintf('_security_%s', $firewallContextName);
        $this->firewallContextName = $firewallContextName;
    }

    public function impersonate(SymfonyUserInterface $user): void
    {
        $token = new UsernamePasswordToken(
            $user,
            $this->firewallContextName,
            array_map(static fn (object|string $role): string => $role, $user->getRoles()),
        );

        $session = $this->requestStack->getSession();
        $session->set($this->sessionTokenParameter, serialize($token));
        $session->save();

        Assert::isInstanceOf($user, SyliusUserInterface::class);

        $this->eventDispatcher->dispatch(new UserEvent($user), UserEvents::SECURITY_IMPERSONATE);
    }
}
