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
use Sylius\Behat\Service\DriverHelper;
use Symfony\Component\Routing\RouterInterface;

class CreatePage extends SymfonyPage implements CreatePageInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private readonly string $routeName,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function create(): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->getDocument()->find('css', 'body')->click();
            $this->waitForFormUpdate();
        }
        $this->getDocument()->pressButton('Create');
    }

    public function getValidationMessage(string $element, array $parameters = []): string
    {
        $foundElement = $this->getFieldElement($element, $parameters);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $foundElement->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $validationMessage->getText();
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function cancelChanges(): void
    {
        $this->getElement('cancel_button')->click();
    }

    public function getMessageInvalidForm(): string
    {
        return $this->getDocument()->find('css', '.ui.icon.negative.message')->getText();
    }

    /**
     * @return array<string, string>
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'form' => 'form',
            'cancel_button' => '[data-test-cancel-changes-button]',
        ]);
    }

    protected function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, function () use ($form) {
            return !$form->hasAttribute('busy');
        });
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
    private function getFieldElement(string $element, array $parameters = []): ?NodeElement
    {
        $element = $this->getElement($element, $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
