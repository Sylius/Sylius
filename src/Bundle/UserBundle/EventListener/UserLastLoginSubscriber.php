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

namespace Sylius\Bundle\UserBundle\EventListener;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class UserLastLoginSubscriber implements EventSubscriberInterface
{
    private ?\DateInterval $trackInterval;

    public function __construct(
        private ObjectManager $userManager,
        private string $userClass,
        ?string $trackInterval,
    ) {
        $this->trackInterval = null === $trackInterval ? null : new \DateInterval($trackInterval);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            UserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
        ];
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->updateUserLastLogin($event->getAuthenticationToken()->getUser());
    }

    public function onImplicitLogin(UserEvent $event)
    {
        $this->updateUserLastLogin($event->getUser());
    }

    private function updateUserLastLogin(mixed $user): void
    {
        if (!$this->shouldUserBeUpdated($user)) {
            return;
        }

        $user->setLastLogin(new \DateTime());
        $this->userManager->persist($user);
        $this->userManager->flush();
    }

    private function shouldUserBeUpdated(mixed $user): bool
    {
        if (!$user instanceof $this->userClass) {
            return false;
        }

        if (!$user instanceof UserInterface) {
            throw new \UnexpectedValueException('In order to use this subscriber, your class has to implement UserInterface');
        }

        if (null === $this->trackInterval || null === $user->getLastLogin()) {
            return true;
        }

        return $user->getLastLogin() <= (new \DateTime())->sub($this->trackInterval);
    }
}
