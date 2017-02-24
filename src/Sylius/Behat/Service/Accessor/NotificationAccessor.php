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
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\NotificationType;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class NotificationAccessor implements NotificationAccessorInterface
{
    const NOTIFICATION_ELEMENT_CSS = '#sylius-flash-messages';

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
    public function getMessage()
    {
        return $this->getMessageElement()->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        if ($this->getMessageElement()->hasClass('positive')) {
            return NotificationType::success();
        }

        if ($this->getMessageElement()->hasClass('negative')) {
            return NotificationType::failure();
        }

        throw new \RuntimeException('Cannot resolve notification type');
    }

    /**
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    private function getMessageElement()
    {
        $messageElement = $this->session->getPage()->find('css', self::NOTIFICATION_ELEMENT_CSS);

        if (null === $messageElement) {
            throw new ElementNotFoundException($this->session->getDriver(), 'message element', 'css', self::NOTIFICATION_ELEMENT_CSS);
        }

        return $messageElement;
    }
}
