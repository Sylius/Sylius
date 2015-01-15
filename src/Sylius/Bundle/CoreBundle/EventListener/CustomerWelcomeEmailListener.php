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
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

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
     *
     * @throws UnexpectedTypeException
     */
    public function handleEvent(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            throw new UnexpectedTypeException(
                $user,
                'Sylius\Component\Core\Model\UserInterface'
            );
        }

        if (!$user->isEnabled()) {
            return;
        }

        $this->mailer->sendCustomerWelcome($user);
    }
}
