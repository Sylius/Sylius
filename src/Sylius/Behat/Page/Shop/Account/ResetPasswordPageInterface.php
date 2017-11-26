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
    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     */
    public function checkValidationMessageFor(string $element, string $message): bool;

    public function reset(): void;

    /**
     * @param string $email
     */
    public function specifyEmail(string $email): void;
}
