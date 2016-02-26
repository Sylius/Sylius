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
use Sylius\Component\Resource\Repository\RepositoryInterface;

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
     * @Transform :zone zone
     * @Transform zone :zone
     * @Transform :zone
     */
    public function getZoneByCode($zone)
    {
        $existingZone = $this->zoneRepository->findOneBy(['code' => $zone]);
        if (null === $existingZone) {
            throw new \InvalidArgumentException(sprintf('Zone with code "%s" does not exist.', $zone));
        }

        return $existingZone;
    }

    /**
     * @Transform /^rest of the world$/
     * @Transform /^the rest of the world$/
     */
    public function getRestOfTheWorldZone()
    {
        $zone = $this->zoneRepository->findOneBy(['code' => 'RoW']);
        if (null === $zone) {
            throw new \Exception('Rest of the world zone does not exist.');
        }

        return $zone;
    }
}
