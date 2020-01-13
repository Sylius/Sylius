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
    /** @var SessionInterface */
    private $session;

    /** @var string */
    private $sessionTokenParameter;

    /** @var string */
    private $firewallContextName;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(SessionInterface $session, string $firewallContextName, EventDispatcherInterface $eventDispatcher)
    {
        $this->session = $session;
        $this->sessionTokenParameter = sprintf('_security_%s', $firewallContextName);
        $this->firewallContextName = $firewallContextName;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function impersonate(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), $this->firewallContextName, $user->getRoles());
        $this->session->set($this->sessionTokenParameter, serialize($token));
        $this->session->save();

        $this->eventDispatcher->dispatch(UserEvents::SECURITY_IMPERSONATE, new UserEvent($user));
    }
}
