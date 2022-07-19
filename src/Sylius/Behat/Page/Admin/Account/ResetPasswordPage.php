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

namespace Sylius\Behat\Page\Admin\Account;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ResetPasswordPage extends SymfonyPage implements ResetPasswordPageInterface
{
    public function specifyNewPassword(string $password): void
    {
        $this->getElement('new_password')->setValue($password);
    }

    public function specifyPasswordConfirmation(string $password): void
    {
        $this->getElement('confirm_new_password')->setValue($password);
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_render_password_reset';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'confirm_new_password' => '[data-test-confirm-new-password]',
            'new_password' => '[data-test-new-password]',
        ]);
    }
}
