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
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'password' => '#sylius_user_reset_password_password_first',
            'confirm_password' => '#sylius_user_reset_password_password_second',
        ]);
    }
}
