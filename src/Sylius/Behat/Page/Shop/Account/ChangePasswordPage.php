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

namespace Sylius\Behat\Page\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\Context\Ui\Admin\Helper\SecurePasswordTrait;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\Routing\RouterInterface;

class ChangePasswordPage extends SymfonyPage implements ChangePasswordPageInterface
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

    public function getRouteName(): string
    {
        return 'sylius_shop_account_change_password';
    }

    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $errorLabel = $this->getElement($element)->getParent()->find('css', '[data-test-validation-error]');

        if (null === $errorLabel) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        return $message === $errorLabel->getText();
    }

    public function specifyCurrentPassword(string $password): void
    {
        $this->getElement('current_password')->setValue($this->retrieveSecurePassword($password));
    }

    public function specifyNewPassword(string $password): void
    {
        $this->getElement('new_password')->setValue($this->replaceWithSecurePassword($password));
    }

    public function specifyConfirmationPassword(string $password): void
    {
        $this->getElement('confirmation')->setValue($this->confirmSecurePassword($password));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'confirmation' => '[data-test-confirmation-new-password]',
            'current_password' => '[data-test-current-password]',
            'new_password' => '[data-test-new-password]',
        ]);
    }
}
