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
        return $this->hasMessage(sprintf('Success %s has been successfully created.', ucfirst($resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyUpdatedFor($resourceName)
    {
        return $this->hasMessage(sprintf('Success %s has been successfully updated.', ucfirst($resourceName)));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccessfullyDeletedFor($resourceName)
    {
        return $this->hasMessage(sprintf('Success %s has been successfully deleted.', ucfirst($resourceName)));
    }

    /**
     * @return NodeElement
     *
     * @throws ElementNotFoundException
     */
    private function getMessageElement()
    {
        $messageElement = $this->createMessageElement();
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
        if (!$element->has('xpath', $element->getXpath())) {
            throw new ElementNotFoundException(sprintf('%s element is not present on the page', self::NOTIFICATION_ELEMENT_CSS));
        }
    }

    /**
     * @return NodeElement
     */
    private function createMessageElement()
    {
        return new NodeElement(
            $this->getSelectorAsXpath(self::NOTIFICATION_ELEMENT_CSS, $this->session->getSelectorsHandler()),
            $this->session
        );
    }

    /**
     * @param string|array $selector
     * @param SelectorsHandler $selectorsHandler
     *
     * @return string
     */
    private function getSelectorAsXpath($selector, SelectorsHandler $selectorsHandler)
    {
        $selectorType = is_array($selector) ? key($selector) : 'css';
        $locator = is_array($selector) ? $selector[$selectorType] : $selector;

        return $selectorsHandler->selectorToXpath($selectorType, $locator);
    }
}
