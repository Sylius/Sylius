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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Updater;

use Sylius\Component\Addressing\Model\ZoneInterface;

final class ZoneUpdater implements ZoneUpdaterInterface
{
    public function update(ZoneInterface $zone, array $attributes): void
    {
        $zone->setCode($attributes['code']);
        $zone->setName($attributes['name']);
        $zone->setType($attributes['type']);
        $zone->setScope($attributes['scope']);

        foreach ($attributes['members'] as $member) {
            $zone->addMember($member);
        }
    }
}
