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

namespace Sylius\Behat\Page\Shop;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

abstract class Page extends SymfonyPage implements PageInterface
{
    public function fillElement(string $value, string $element, array $parameters = []): void
    {
        $foundElement = $this->getElement($element, $parameters);
        $foundElement->setValue($value);
    }

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

    protected function waitForElementUpdate(string $element): void
    {
        $element = $this->getElement($element);

        usleep(500000); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $element->waitFor(1500, fn () => !$element->hasAttribute('busy'));
    }

    /**
     * @param array<string, string> $parameters
     *
     * @throws ElementNotFoundException
     */
    protected function getFieldElement(string $element, array $parameters): NodeElement
    {
        $element = $this->getElement($element, $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
