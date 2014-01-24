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

use FOS\UserBundle\Event\FormEvent;
use Sylius\Bundle\CoreBundle\Generator\CustomerNumberGeneratorInterface;
use Sylius\Bundle\CoreBundle\Model\UserInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Listener to generate customer number via event
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class CustomerNumberListener
{
    /**
     * @var CustomerNumberGeneratorInterface
     */
    protected $generator;

    /**
     * Constructor
     *
     * @param CustomerNumberGeneratorInterface $generator
     */
    public function __construct(CustomerNumberGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param FormEvent|GenericEvent $event
     * @throws \InvalidArgumentException
     */
    public function generateCustomerNumber(Event $event)
    {
        if ($event instanceof FormEvent) {
            $user = $event->getForm()->getData();
        } else if ($event instanceof GenericEvent) {
            $user = $event->getSubject();
        } else {
            throw new \InvalidArgumentException(
                'Customer number listener requires event to be instance of "Sylius\Bundle\CoreBundle\Model\UserInterface" or ""'
            );
        }

        if (!$user instanceof UserInterface) {
            throw new \InvalidArgumentException(
                'Customer number listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\UserInterface"'
            );
        }

        $this->generator->generate($user);
    }
}
