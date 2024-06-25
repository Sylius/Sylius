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

namespace Sylius\Behat\Element\Admin\Customer;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;

class FormElement extends BaseFormElement implements FormElementInterface
{
    use NamesIt;
    use SpecifiesItsField;
    use Toggles;

    public function getFullName(): string
    {
        $firstNameElement = $this->getElement('first_name')->getValue();
        $lastNameElement = $this->getElement('last_name')->getValue();

        return sprintf('%s %s', $firstNameElement, $lastNameElement);
    }

    public function getFirstName(): string
    {
        return $this->getElement('first_name')->getValue();
    }

    public function getLastName(): string
    {
        return $this->getElement('last_name')->getValue();
    }

    public function getPassword(): string
    {
        return $this->getElement('password')->getValue();
    }

    public function subscribeToTheNewsletter(): void
    {
        $this->getDocument()->checkField('Subscribe to the newsletter');
    }

    public function isSubscribedToTheNewsletter(): bool
    {
        return $this->getDocument()->hasCheckedField('Subscribe to the newsletter');
    }

    public function getGroupName(): string
    {
        return $this->getElement('group')->getText();
    }

    public function verifyUser(): void
    {
        $this->getDocument()->checkField('Verified');
    }

    public function specifyFirstName(string $name): void
    {
        $this->getElement('first_name')->setValue($name);
    }

    public function specifyLastName(string $name): void
    {
        $this->getElement('last_name')->setValue($name);
    }

    public function specifyEmail(string $email): void
    {
        $this->getElement('email')->setValue($email);
    }

    public function specifyBirthday(string $birthday): void
    {
        $this->getElement('birthday')->setValue($birthday);
    }

    public function specifyPassword(string $password): void
    {
        $this->getElement('password')->setValue($password);
    }

    public function chooseGender(string $gender): void
    {
        $this->getElement('gender')->selectOption($gender);
    }

    public function chooseGroup(string $group): void
    {
        $this->getElement('group')->selectOption($group);
    }

    protected function getToggleableElement(): NodeElement
    {
        return $this->getElement('enabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'birthday' => '[data-test-birthday]',
            'email' => '[data-test-email]',
            'enabled' => '[data-test-enabled]',
            'first_name' => '[data-test-first-name]',
            'gender' => '[data-test-gender]',
            'group' => '[data-test-group]',
            'last_name' => '[data-test-last-name]',
            'password' => '[data-test-password]',
        ]);
    }
}
