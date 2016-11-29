<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Addressing\Factory;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
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
    public function createNew()
    {
        return $this->factory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createTyped($type)
    {
        /* @var ZoneInterface $zone */
        $zone = $this->createNew();
        $zone->setType($type);

        return $zone;
    }

    /**
     * {@inheritdoc}
     */
    public function createWithMembers(array $membersCodes)
    {
        /* @var ZoneInterface $zone */
        $zone = $this->createNew();
        foreach ($membersCodes as $memberCode) {
            $zoneMember = $this->zoneMemberFactory->createNew();
            $zoneMember->setCode($memberCode);

            $zone->addMember($zoneMember);
        }

        return $zone;
    }
}
