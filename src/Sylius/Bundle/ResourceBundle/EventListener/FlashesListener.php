<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Sylius\Bundle\ResourceBundle\Event\GenericResourceEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class FlashesListener
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param GenericResourceEvent $event
     */
    public function addSuccessFlash(GenericResourceEvent $event)
    {
        $this->addFlash('success', $this->getMessage($event));
    }

    /**
     * @param string $type
     * @param string $message
     */
    private function addFlash($type, $message)
    {
        $this->session->getBag('flashes')->add($type, $message);
    }

    /**
     * @param GenericResourceEvent $event
     *
     * @return string
     */
    private function getMessage(GenericResourceEvent $event)
    {
        $metadata = $event->getMetadata();
        $requestConfiguration = $event->getRequestConfiguration();

        $defaultMessage = sprintf('%s.%s.%s', $metadata->getApplicationName(), $metadata->getResourceName(), $event->getActionName());

        return $requestConfiguration->getFlashMessage() ?: $defaultMessage;
    }
}
