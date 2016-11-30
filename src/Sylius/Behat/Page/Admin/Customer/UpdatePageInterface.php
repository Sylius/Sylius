<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Customer;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable();
    public function disable();

    /**
     * @return string
     */
    public function getFullName();
    
    /**
     * @param string $firstName
     */
    public function changeFirstName($firstName);

    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @param string $lastName
     */
    public function changeLastName($lastName);

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param string $email
     */
    public function changeEmail($email);

    /**
     * @param string $password
     */
    public function changePassword($password);

    /**
     * @return string
     */
    public function getPassword();

    public function subscribeToTheNewsletter();

    /**
     * @return bool
     */
    public function isSubscribedToTheNewsletter();

    /**
     * @return string
     */
    public function getGroupName();
}
