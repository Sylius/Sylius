<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service\Accessor;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class NotificationAccessor implements NotificationAccessorInterface
{
    const NOTIFICATION_ELEMENT_CSS = '.message';

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function hasSuccessMessage()
    {
        $messageElement = $this->getMessageElement();
        if (null === $messageElement) {
            return false;
        }

        return $this->getMessageElement()->hasClass('positive');
    }

    /**
     * {@inheritdoc}
     */
    public function hasMessage($message)
    {
        $messageElement = $this->getMessageElement();
        if (null === $messageElement) {
            return false;
        }

        return $message === $messageElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        $messageElement = $this->getMessageElement();
        if (null === $messageElement) {
            return '';
        }

        return $messageElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        $messageElement = $this->getMessageElement();
        if (null === $messageElement) {
            return '';
        }
        
        if ($this->getMessageElement()->hasClass('positive')) {
            return 'success';
        }
        
        if ($this->getMessageElement()->hasClass('negative')) {
            return 'failure';
        }

        return '';
    }

    /**
     * @return NodeElement
     */
    private function getMessageElement()
    {
        return $this->session->getPage()->find('css', self::NOTIFICATION_ELEMENT_CSS);
    }
}
