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

class ResetPasswordPage extends SymfonyPage implements ResetPasswordPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_password_reset';
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->getDocument()->pressButton('Reset');
    }

    /**
     * {@inheritdoc}
     */
    public function specifyNewPassword(string $password): void
    {
        $this->getElement('password')->setValue($password);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyConfirmPassword(string $password): void
    {
        $this->getElement('confirm_password')->setValue($password);
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '.sylius-validation-error');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $message === $errorLabel->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'password' => '#sylius_user_reset_password_password_first',
            'confirm_password' => '#sylius_user_reset_password_password_second',
        ]);
    }
}
