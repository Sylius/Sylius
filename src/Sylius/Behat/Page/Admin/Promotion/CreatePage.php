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

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsField;

    public function getValidationMessage(string $element): string
    {
        $foundElement = $this->getFieldElement($element);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $foundElement->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $validationMessage->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_promotion_code',
            'form' => '[data-live-name-value="sylius_admin:promotion:form"]',
            'rules' => '#sylius_promotion_rules',
        ]);
    }

    /** @throws ElementNotFoundException */
    private function getFieldElement(string $element): ?NodeElement
    {
        $element = $this->getElement($element);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
