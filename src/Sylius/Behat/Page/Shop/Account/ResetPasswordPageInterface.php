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

use Sylius\Behat\Page\SymfonyPageInterface;

interface ResetPasswordPageInterface extends SymfonyPageInterface
{
    public function reset(): void;

    /**
     * @param string $password
     */
    public function specifyNewPassword(string $password): void;

    /**
     * @param string $password
     */
    public function specifyConfirmPassword(string $password): void;

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     */
    public function checkValidationMessageFor(string $element, string $message): bool;
}
