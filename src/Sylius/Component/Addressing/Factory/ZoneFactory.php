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

namespace Sylius\Component\Addressing\Factory;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ZoneFactory implements ZoneFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var FactoryInterface
     */
    private $zoneMemberFactory;

    /**
     * @param FactoryInterface $factory
     * @param FactoryInterface $zoneMemberFactory
     */
    public function __construct(FactoryInterface $factory, FactoryInterface $zoneMemberFactory)
    {
        $this->factory = $factory;
        $this->zoneMemberFactory = $zoneMemberFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): ZoneInterface
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createTyped(string $type): ZoneInterface
    {
        /** @var ZoneInterface $zone */
        $zone = $this->createNew();
        $zone->setType($type);

        return $zone;
    }

    /**
     * {@inheritdoc}
     */
    public function createWithMembers(array $membersCodes): ZoneInterface
    {
        /** @var ZoneInterface $zone */
        $zone = $this->createNew();
        foreach ($membersCodes as $memberCode) {
            /** @var ZoneMemberInterface $zoneMember */
            $zoneMember = $this->zoneMemberFactory->createNew();
            $zoneMember->setCode($memberCode);

            $zone->addMember($zoneMember);
        }

        return $zone;
    }
}
