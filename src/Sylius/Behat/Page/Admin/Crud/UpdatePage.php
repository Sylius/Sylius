<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Crud;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Component\Core\Formatter\StringInflector;
use Symfony\Component\Routing\RouterInterface;

class UpdatePage extends SymfonyPage implements UpdatePageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private string $routeName,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function saveChanges(): void
    {
        $this->getDocument()->pressButton('sylius_save_changes_button');
    }

    public function cancelChanges(): void
    {
        $this->getDocument()->find('css', '[data-test-cancel-changes-button]')->click();
    }

    public function getValidationMessage(string $element): string
    {
        $foundElement = $this->getFieldElement($element);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $foundElement->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $validationMessage->getText();
    }

    public function hasResourceValues(array $parameters): bool
    {
        foreach ($parameters as $element => $value) {
            if ($this->getElement($element)->getValue() !== (string) $value) {
                return false;
            }
        }

        return true;
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getMessageInvalidForm(): string
    {
        return $this->getDocument()->find('css', '.ui.icon.negative.message')->getText();
    }

    protected function verifyStatusCode(): void
    {
        try {
            $statusCode = $this->getSession()->getStatusCode();
        } catch (DriverException) {
            return; // Ignore drivers which cannot check the response status code
        }

        if (($statusCode >= 200 && $statusCode <= 299) || $statusCode === 422) {
            return;
        }

        $currentUrl = $this->getSession()->getCurrentUrl();
        $message = sprintf('Could not open the page: "%s". Received an error status code: %s', $currentUrl, $statusCode);

        throw new UnexpectedPageException($message);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element): ?NodeElement
    {
        $element = $this->getElement(StringInflector::nameToCode($element));
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
