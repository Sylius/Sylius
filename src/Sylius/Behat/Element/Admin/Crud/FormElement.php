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

namespace Sylius\Behat\Element\Admin\Crud;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;

class FormElement extends Element implements FormElementInterface
{
    private ?NodeElement $form = null;

    /**
     * @param array<string, string> $parameters
     */
    public function getValidationMessage(string $element, array $parameters = []): string
    {
        $foundElement = $this->getFieldElement($element, $parameters);

        $validationMessage = $foundElement->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $validationMessage->getText();
    }

    public function getValidationErrors(): string
    {
        $validationMessage = $this->getDocument()->find('css', '.sylius-validation-error, .alert.alert-danger');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error, .alert.alert-danger');
        }

        return $validationMessage->getText();
    }

    /**
     * @return array<string, string>
     */
    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(), [
            'form' => 'form',
        ]);
    }

    protected function waitForFormUpdate(): void
    {
        if (null === $this->form) {
            $this->form = $this->getElement('form');
        }

        usleep(500000); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $this->form->waitFor(1500, fn () => !$this->form->hasAttribute('busy'));
    }

    /**
     * @param array<string, string> $parameters
     *
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element, array $parameters): NodeElement
    {
        $element = $this->getElement($element, $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
