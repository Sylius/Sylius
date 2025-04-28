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

namespace Sylius\Behat\Context\Ui\Admin\Helper;

trait SecurePasswordTrait
{
    private function replaceWithSecurePassword(string $password): string
    {
        $this->sharedStorage->set('scenario_setup_password', $password);

        // If the password is empty or less than 4 characters, use the provided password to satisfy input validation
        $newPassword = (empty($password) || strlen($password) < 4)
            ? $password
            : bin2hex(random_bytes(16));

        $this->sharedStorage->set('password', $newPassword);

        return $newPassword;
    }

    private function confirmSecurePassword(string $password): string
    {
        return $password === $this->sharedStorage->get('scenario_setup_password')
            ? $this->sharedStorage->get('password')
            : $password
        ;
    }

    private function retrieveSecurePassword(string $password): string
    {
        $scenario_setup_password = $this->sharedStorage->get('scenario_setup_password');

        // If the provided password matches the scenario setup password,
        // use the secure password generated earlier; otherwise, return the scenario setup password to cause the test to fail
        return $scenario_setup_password === $password ? $this->sharedStorage->get('password') : $scenario_setup_password;
    }
}
