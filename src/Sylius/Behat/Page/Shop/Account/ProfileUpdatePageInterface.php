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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ProfileUpdatePageInterface extends PageInterface
{
    /**
     * @param string $firstName
     */
    public function specifyFirstName($firstName);

    /**
     * @param string $lastName
     */
    public function specifyLastName($lastName);

    /**
     * @param string $email
     */
    public function specifyEmail($email);

    public function saveChanges();

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor($element, $message);

    public function subscribeToTheNewsletter();

    /**
     * @return bool
     */
    public function isSubscribedToTheNewsletter();
}
