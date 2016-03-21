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
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use Sylius\Behat\Page\ElementNotFoundException;

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
        try {
            return $this->getMessageElement()->hasClass('positive');
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasMessage($message)
    {
        try {
            return $message === $this->getMessageElement()->getText();
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyCreatedFor($resourceName)
    {
        return $this->hasMessage(sprintf('Success %s has been successfully created.', $this->humanizeResourceName($resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyUpdatedFor($resourceName)
    {
        return $this->hasMessage(sprintf('Success %s has been successfully updated.', $this->humanizeResourceName($resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyDeletedFor($resourceName)
    {
        return $this->hasMessage(sprintf('Success %s has been successfully deleted.', $this->humanizeResourceName($resourceName)));
    }

    /**
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    private function getMessageElement()
    {
        $messageElement = $this->session->getPage()->find('css', self::NOTIFICATION_ELEMENT_CSS);
        $this->assertElementExistsOnPage($messageElement);

        return $messageElement;
    }

    /**
     * @param NodeElement $element
     *
     * @throws ElementNotFoundException
     */
    private function assertElementExistsOnPage(NodeElement $element)
    {
        if (null === $element) {
            throw new ElementNotFoundException(sprintf('%s element is not present on the page', self::NOTIFICATION_ELEMENT_CSS));
        }
    }

    /**
     * @param string $resourceName
     *
     * @return string
     */
    private function humanizeResourceName($resourceName)
    {
        return ucfirst(str_replace('_', ' ', $resourceName));
    }
}
