<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLastLoginSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    protected $userManager;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @param ObjectManager $userManager
     * @param string $userClass
     */
    public function __construct(ObjectManager $userManager, $userClass)
    {
        $this->userManager = $userManager;
        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
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
        $user = $event->getAuthenticationToken()->getUser();
        $this->updateUserLastLogin($user);
    }

    /**
     * @param UserEvent $event
     */
    public function onImplicitLogin(UserEvent $event)
    {
        $this->updateUserLastLogin($event->getUser());
    }

    /**
     * @param UserInterface $user
     */
    protected function updateUserLastLogin($user)
    {
        if ($user instanceof $this->userClass) {
            $user->setLastLogin(new \DateTime());
            $this->userManager->persist($user);
            $this->userManager->flush();
        }
    }
}
