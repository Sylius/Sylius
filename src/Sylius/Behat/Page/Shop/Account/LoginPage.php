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

use Behat\Mink\Session;
use Sylius\Behat\Context\Ui\Admin\Helper\SecurePasswordTrait;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\Routing\RouterInterface;

class LoginPage extends SymfonyPage implements LoginPageInterface
{
    use SecurePasswordTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        protected TableAccessorInterface $tableAccessor,
        private SharedStorageInterface $sharedStorage,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_login';
    }

    public function hasValidationErrorWith(string $message): bool
    {
        return $this->getElement('flash_message')->getText() === $message;
    }

    public function logIn(): void
    {
        $this->getElement('login_button')->click();

        DriverHelper::waitForPageToLoad($this->getSession());
    }

    public function specifyPassword(string $password): void
    {
        $this->getElement('password')->setValue($this->retrieveSecurePassword($password));
    }

    public function specifyUsername(string $username): void
    {
        $this->getElement('username')->setValue($username);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'login_button' => '[data-test-button="login-button"]',
            'password' => '[data-test-login-password]',
            'username' => '[data-test-login-username]',
            'flash_message' => '[data-test-sylius-flash-message]',
        ]);
    }
}
