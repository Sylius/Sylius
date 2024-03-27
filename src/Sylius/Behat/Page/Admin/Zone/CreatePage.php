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

namespace Sylius\Behat\Page\Admin\Zone;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsField;

    public function addMember(): void
    {
        $this->getDocument()->clickLink('Add member');
    }

    public function checkValidationMessageForMembers(string $message): bool
    {
        $membersValidationElement = $this->getElement('ui_segment')->find('css', '.sylius-validation-error');
        if (null === $membersValidationElement) {
            throw new ElementNotFoundException($this->getDriver(), 'members validation box', 'css', '.sylius-validation-error');
        }

        return $membersValidationElement->getText() === $message;
    }

    public function chooseMember(string $name): void
    {
        $selectItems = $this->getDocument()->waitFor(2, fn () => $this->getDocument()->findAll('css', 'div[data-form-type="collection"] select'));
        $lastSelectItem = end($selectItems);

        if (false === $lastSelectItem) {
            throw new ElementNotFoundException($this->getSession(), 'select', 'css', 'div[data-form-type="collection"] select');
        }

        $lastSelectItem->selectOption($name);
    }

    public function selectScope(string $scope): void
    {
        $this->getDocument()->selectFieldOption('Scope', $scope);
    }

    public function hasType(string $type): bool
    {
        $typeField = $this->getElement('type');
        $selectedOption = $typeField->find('css', 'option[selected]');

        return lcfirst($selectedOption->getText()) === $type;
    }

    public function isTypeFieldDisabled(): bool
    {
        return $this->getElement('type')->hasAttribute('disabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_zone_code',
            'name' => '#sylius_zone_name',
            'type' => '#sylius_zone_type',
            'ui_segment' => '.ui.segment',
        ]);
    }
}
