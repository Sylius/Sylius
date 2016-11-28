<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ZoneContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $zoneRepository;

    /**
     * @param RepositoryInterface $zoneRepository
     */
    public function __construct(RepositoryInterface $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * @Transform /^"([^"]+)" zone$/
     * @Transform /^zone "([^"]+)"$/
     * @Transform :zone
     */
    public function getZoneByCode($code)
    {
        return $this->getZoneBy(['code' => $code]);
    }

    /**
     * @Transform /^zone named "([^"]+)"$/
     */
    public function getZoneByName($name)
    {
        return $this->getZoneBy(['name' => $name]);
    }

    /**
     * @Transform /^rest of the world$/
     */
    public function getRestOfTheWorldZone()
    {
        $zone = $this->zoneRepository->findOneBy(['code' => 'RoW']);
        Assert::notNull(
            $zone,
            'Rest of the world zone does not exist.'
        );

        return $zone;
    }

    /**
     * @param array $parameters
     *
     * @return ZoneInterface
     */
    private function getZoneBy(array $parameters)
    {
        $existingZone = $this->zoneRepository->findOneBy($parameters);
        Assert::notNull(
            $existingZone,
            'Zone does not exist.'
        );

        return $existingZone;
    }
}
