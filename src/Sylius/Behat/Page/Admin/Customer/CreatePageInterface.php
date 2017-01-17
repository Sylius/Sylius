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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $name
     */
    public function specifyFirstName($name);

    /**
     * @param string $name
     */
    public function specifyLastName($name);

    /**
     * @param string $email
     */
    public function specifyEmail($email);

    /**
     * @param string $birthday
     */
    public function specifyBirthday($birthday);

    /**
     * @param string $password
     */
    public function specifyPassword($password);

    /**
     * @param string $gender
     */
    public function chooseGender($gender);

    /**
     * @param string $group
     */
    public function chooseGroup($group);

    public function selectCreateAccount();

    /**
     * @return bool
     */
    public function hasPasswordField();

    /**
     * @return bool
     */
    public function hasCheckedCreateOption();

    /**
     * @return bool
     */
    public function isUserFormHidden();
}
