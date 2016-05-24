<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowksi@lakion.com>
 */
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
     * @param string $password
     */
    public function specifyCurrentPassword($password)
    {
        $this->getElement('current password')->setValue($password);
    }

    /**
     * @param string $password
     */
    public function specifyNewPassword($password)
    {
        $this->getElement('new password')->setValue($password);
    }

    /**
     * @param string $password
     */
    public function specifyConfirmationPassword($password)
    {
        $this->getElement('confirmation')->setValue($password);
    }

    /**
     * {@inheritdoc}
     */
    public function checkValidationMessageFor($element, $message)
    {
        $foundedElement = $this->getFieldElement($element);
        if (null === $foundedElement) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.pointing');
        }

        return $message === $foundedElement->find('css', '.form-error')->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'current password' => '#sylius_user_change_password_currentPassword',
            'new password' => '#sylius_user_change_password_newPassword_first',
            'confirmation' => '#sylius_user_change_password_newPassword_second',
        ]);
    }

    /**
     * @param string $element
     *
     * @return \Behat\Mink\Element\NodeElement|null
     *
     * @throws ElementNotFoundException
     */
    private function getFieldElement($element)
    {
        $element = $this->getElement($element);
        while (null !== $element && !($element->hasClass('field'))) {
            $element = $element->getParent();
        }

        return $element;
    }
}
