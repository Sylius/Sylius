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

namespace Sylius\Component\Addressing\Factory;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of ZoneInterface
 *
 * @implements ZoneFactoryInterface<T>
 */
final class ZoneFactory implements ZoneFactoryInterface
{
    /**
     * @param FactoryInterface<ZoneInterface> $factory
     * @param FactoryInterface<ZoneMemberInterface> $zoneMemberFactory
     */
    public function __construct(private FactoryInterface $factory, private FactoryInterface $zoneMemberFactory)
    {
    }

    public function createNew(): ZoneInterface
    {
        return $this->factory->createNew();
    }

    public function createTyped(string $type): ZoneInterface
    {
        $zone = $this->createNew();
        $zone->setType($type);

        return $zone;
    }

    public function createWithMembers(array $membersCodes): ZoneInterface
    {
        $zone = $this->createNew();
        foreach ($membersCodes as $memberCode) {
            $zoneMember = $this->zoneMemberFactory->createNew();
            $zoneMember->setCode($memberCode);

            $zone->addMember($zoneMember);
        }

        return $zone;
    }
}
