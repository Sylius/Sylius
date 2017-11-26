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

namespace Sylius\Behat\Page\Admin\Zone;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $name
     */
    public function nameIt(string $name): void;

    /**
     * @return int
     */
    public function countMembers(): int;

    /**
     * @return string
     */
    public function getScope(): string;

    /**
     *
     * @return bool
     */
    public function hasMember(ZoneMemberInterface $zoneMember): bool;

    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param ZoneMemberInterface $zoneMember
     */
    public function removeMember(ZoneMemberInterface $zoneMember): void;
}
