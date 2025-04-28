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

use Behat\Mink\Session;
use Sylius\Behat\Context\Ui\Admin\Helper\SecurePasswordTrait;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\SharedStorageInterface;
use Symfony\Component\Routing\RouterInterface;

class LoginPage extends SymfonyPage implements LoginPageInterface
{
    use SecurePasswordTrait;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private SharedStorageInterface $sharedStorage,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function hasValidationErrorWith(string $message): bool
    {
        return $this->getElement('validation_error')->getText() === $message;
    }

    public function logIn(): void
    {
        $this->getDocument()->pressButton('Login');
    }

    public function specifyPassword(string $password): void
    {
        $this->getDocument()->fillField('Password', $this->retrieveSecurePassword($password));
    }

    public function specifyUsername(string $username): void
    {
        $this->getDocument()->fillField('Username', $username);
    }

    public function getRouteName(): string
    {
        return 'sylius_admin_login';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'validation_error' => '[data-test-invalid-credentials-message]',
        ]);
    }
}
