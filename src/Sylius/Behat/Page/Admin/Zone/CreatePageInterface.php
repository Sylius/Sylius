<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Zone;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    public function addMember();

    /**
     * @param string $message
     */
    public function checkValidationMessageForMembers($message);

    /**
     * @param string $name
     */
    public function chooseMember($name);

    /**
     * @param string $scope
     */
    public function selectScope($scope);

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasType($type);

    /**
     * @return bool
     */
    public function isTypeFieldDisabled();

    /**
     * @param string $name
     */
    public function nameIt($name);

    /**
     * @param string $code
     */
    public function specifyCode($code);
}
