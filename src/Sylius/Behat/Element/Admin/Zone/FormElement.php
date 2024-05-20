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

namespace Sylius\Behat\Element\Admin\Zone;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

class FormElement extends BaseFormElement implements FormElementInterface
{
    use NamesIt;
    use SpecifiesItsField;
    use ChecksCodeImmutability;

    public function getName(): string
    {
        return $this->getElement('name')->getValue();
    }

    public function getType(): string
    {
        return $this->getElement('type')->getValue();
    }

    public function isTypeFieldDisabled(): bool
    {
        return $this->getElement('type')->hasAttribute('disabled');
    }

    public function getScope(): string
    {
        return $this->getElement('scope')->getValue();
    }

    public function selectScope(string $scope): void
    {
        $this->getDocument()->selectFieldOption('Scope', $scope);
    }

    public function hasMember(string $member): bool
    {
        return $this->hasElement('zone_member', ['%name%' => $member]);
    }

    public function countMembers(): int
    {
        return count($this->getElement('zone_members')->findAll('css', '[data-test-zone-member]'));
    }

    public function addMember(): void
    {
        $this->getElement('add_member')->click();
        $this->waitForElement(5, 'zone_member_added');
    }

    public function removeMember(string $member): void
    {
        $this->getElement('zone_member_delete', ['%name%' => $member])->click();
        $this->waitForElement(5, 'zone_member', ['%name%' => $member], false);
    }

    public function chooseMember(string $name): void
    {
        $select = $this->getElement('zone_member_last')->find('css', 'select');
        if (null === $select) {
            throw new ElementNotFoundException($this->getSession(), 'select', 'css', 'select');
        }

        $select->selectOption($name);
    }

    public function getFormValidationMessage(): string
    {
        return $this->getElement('form_validation_message')->getText();
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_member' => '[data-test-add-member]',
            'code' => '[data-test-code]',
            'form' => 'form',
            'form_validation_message' => 'form > div.alert.alert-danger.d-block',
            'name' => '[data-test-name]',
            'scope' => '[data-test-scope]',
            'type' => '[data-test-type]',
            'zone_member' => '[data-test-zone-member]:contains("%name%")',
            'zone_member_added' => '[data-test-zone-member]:last-child option:not([selected="selected"])',
            'zone_member_delete' => '[data-test-zone-member]:contains("%name%") button[name$="[delete]"]',
            'zone_member_last' => '[data-test-members]:last-child',
            'zone_members' => '[data-test-members]',
        ]);
    }

    private function waitForElement(
        int $timeout,
        string $elementName,
        array $parameters = [],
        bool $shouldExist = true,
    ): bool {
        return $this->getDocument()->waitFor(
            $timeout,
            fn (): bool => $shouldExist && $this->hasElement($elementName, $parameters),
        );
    }
}
