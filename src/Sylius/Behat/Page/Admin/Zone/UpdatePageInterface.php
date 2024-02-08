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

namespace Sylius\Behat\Page\Admin\Zone;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function nameIt(string $name): void;

    public function countMembers(): int;

    public function getScope(): string;

    public function hasMember(ZoneMemberInterface $zoneMember): bool;

    public function isCodeDisabled(): bool;

    public function removeMember(ZoneMemberInterface $zoneMember): void;
}
