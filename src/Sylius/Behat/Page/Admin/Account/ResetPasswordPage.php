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

namespace Sylius\Behat\Page\Admin\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\Context\Ui\Admin\Helper\SecurePasswordTrait;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\Routing\RouterInterface;

class ResetPasswordPage extends SymfonyPage implements ResetPasswordPageInterface
{
    use SecurePasswordTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        protected SharedStorageInterface $sharedStorage,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function specifyNewPassword(string $password): void
    {
        $this->getElement('new_password')->setValue($this->replaceWithSecurePassword($password));
    }

    public function specifyPasswordConfirmation(string $password): void
    {
        $this->getElement('confirm_new_password')->setValue($this->confirmSecurePassword($password));
    }

    public function getValidationMessageForNewPassword(): string
    {
        $errorLabel = $this->getElement('new_password')->getParent()->find('css', '.invalid-feedback');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $errorLabel->getText();
    }

    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '.invalid-feedback');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $message === $errorLabel->getText();
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
