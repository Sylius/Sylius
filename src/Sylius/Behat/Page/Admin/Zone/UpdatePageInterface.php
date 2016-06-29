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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Addressing\Model\ZoneMemberInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $name
     */
    public function nameIt($name);

    /**
     * @return int
     */
    public function countMembers();

    /**
     * @param ZoneMemberInterface $zoneMember
     *
     * @return bool
     */
    public function hasMember(ZoneMemberInterface $zoneMember);

    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param ZoneMemberInterface $zoneMember
     */
    public function removeMember(ZoneMemberInterface $zoneMember);
}
