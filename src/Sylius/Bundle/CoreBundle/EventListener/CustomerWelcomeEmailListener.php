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
use Sylius\Bundle\CoreBundle\Mailer\CustomerWelcomeMailerInterface;
use Sylius\Bundle\CoreBundle\Model\UserInterface;

/**
 * Sends Customer welcome email when triggered by event
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CustomerWelcomeEmailListener
{
    /**
     * @var CustomerWelcomeMailerInterface
     */
    protected $mailer;

    /**
     * Constructor
     *
     * @param CustomerWelcomeMailerInterface $mailer
     */
    public function __construct(CustomerWelcomeMailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param FilterUserResponseEvent $event
     * @throws \InvalidArgumentException
     */
    public function handleEvent(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            throw new \InvalidArgumentException(
                'Customer welcome email listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\UserInterface"'
            );
        }

        if (!$user->isEnabled()) {
            return;
        }

        $this->mailer->sendCustomerWelcome($user);
    }
}
