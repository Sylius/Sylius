<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\FOSUserEvents;
use Sylius\Bundle\CoreBundle\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * User profile edition listener
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class ProfileEditionListener implements EventSubscriberInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::PROFILE_EDIT_COMPLETED => 'onProfileEditionCompleted',
        );
    }

    public function onProfileEditionCompleted(FilterUserResponseEvent $event)
    {
        $this->userRepository->generateCustomerId($event->getUser());
    }
}