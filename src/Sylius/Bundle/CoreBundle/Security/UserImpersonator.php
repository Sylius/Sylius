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

use Sylius\Bundle\CoreBundle\Provider\SessionProvider;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface as SyliusUserInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Webmozart\Assert\Assert;

final class UserImpersonator implements UserImpersonatorInterface
{
    private string $sessionTokenParameter;

    private string $firewallContextName;

    public function __construct(
        private RequestStack|SessionInterface $requestStackOrSession,
        string $firewallContextName,
        private EventDispatcherInterface $eventDispatcher,
    ) {
        $this->sessionTokenParameter = sprintf('_security_%s', $firewallContextName);
        $this->firewallContextName = $firewallContextName;

        if ($requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation(
                'sylius/core-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                SessionInterface::class,
                self::class,
                RequestStack::class,
            );
        }
    }

    public function impersonate(SymfonyUserInterface $user): void
    {
        /** @deprecated parameter credential was deprecated in Symfony 5.4, so in Sylius 1.11 too, in Sylius 2.0 providing 4 arguments will be prohibited. */
        if (3 === (new \ReflectionClass(UsernamePasswordToken::class))->getConstructor()->getNumberOfParameters()) {
            $token = new UsernamePasswordToken(
                $user,
                $this->firewallContextName,
                array_map(/** @param object|string $role */ static fn ($role): string => (string) $role, $user->getRoles()),
            );
        } else {
            Assert::methodExists($user, 'getPassword');
            $token = new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                $this->firewallContextName, // @phpstan-ignore-line continue to support Sf < 6
                array_map(/** @param object|string $role */ static fn ($role): string => (string) $role, $user->getRoles()),
            );
        }

        $session = SessionProvider::getSession($this->requestStackOrSession);
        $session->set($this->sessionTokenParameter, serialize($token));
        $session->save();

        Assert::isInstanceOf($user, SyliusUserInterface::class);

        $this->eventDispatcher->dispatch(new UserEvent($user), UserEvents::SECURITY_IMPERSONATE);
    }
}
