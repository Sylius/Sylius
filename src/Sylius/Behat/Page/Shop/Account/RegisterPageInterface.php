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

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface RegisterPageInterface extends SymfonyPageInterface
{
    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     */
    public function checkValidationMessageFor($element, $message);

    public function register();

    /**
     * @param string $email
     */
    public function specifyEmail($email);

    /**
     * @param string $firstName
     */
    public function specifyFirstName($firstName);

    /**
     * @param string $lastName
     */
    public function specifyLastName($lastName);

    /**
     * @param string $password
     */
    public function specifyPassword($password);

    /**
     * @param string $phoneNumber
     */
    public function specifyPhoneNumber($phoneNumber);

    /**
     * @param string $password
     */
    public function verifyPassword($password);

    public function subscribeToTheNewsletter();
}
