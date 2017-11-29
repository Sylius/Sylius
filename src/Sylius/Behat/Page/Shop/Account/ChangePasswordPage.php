<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

class ChangePasswordPage extends SymfonyPage implements ChangePasswordPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_account_change_password';
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidationMessageFor($element, $message)
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '.sylius-validation-error');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $message === $errorLabel->getText();
    }

    public function specifyCurrentPassword(string $password): void
    {
        $this->getElement('current_password')->setValue($password);
    }

    public function specifyNewPassword(string $password): void
    {
        $this->getElement('new_password')->setValue($password);
    }

    public function specifyConfirmationPassword(string $password): void
    {
        $this->getElement('confirmation')->setValue($password);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'confirmation' => '#sylius_user_change_password_newPassword_second',
            'current_password' => '#sylius_user_change_password_currentPassword',
            'new_password' => '#sylius_user_change_password_newPassword_first',
        ]);
    }
}
