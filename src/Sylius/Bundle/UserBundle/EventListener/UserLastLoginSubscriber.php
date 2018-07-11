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

namespace Sylius\Bundle\UserBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class UserLastLoginSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    private $userManager;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @param ObjectManager $userManager
     * @param string $userClass
     */
    public function __construct(ObjectManager $userManager, string $userClass)
    {
        $this->userManager = $userManager;
        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            UserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
        ];
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->updateUserLastLogin($event->getAuthenticationToken()->getUser());
    }

    /**
     * @param UserEvent $event
     */
    public function onImplicitLogin(UserEvent $event)
    {
        $this->updateUserLastLogin($event->getUser());
    }

    private function updateUserLastLogin($user): void
    {
        if (!$user instanceof $this->userClass) {
            return;
        }

        if (!$user instanceof UserInterface) {
            throw new \UnexpectedValueException('In order to use this subscriber, your class has to implement UserInterface');
        }

        $user->setLastLogin(new \DateTime());
        $this->userManager->persist($user);
        $this->userManager->flush();
    }
}
