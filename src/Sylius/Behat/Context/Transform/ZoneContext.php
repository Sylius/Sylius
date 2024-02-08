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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ZoneContext implements Context
{
    public function __construct(private RepositoryInterface $zoneRepository)
    {
    }

    /**
     * @Transform /^"([^"]+)" zone$/
     * @Transform /^zone "([^"]+)"$/
     * @Transform /^zone named "([^"]+)"$/
     * @Transform :zone
     * @Transform :otherZone
     */
    public function getZone(string $codeOrName): ZoneInterface
    {
        $zone = $this->zoneRepository->findOneBy(['code' => $codeOrName]);
        if (null !== $zone) {
            return $zone;
        }

        $zone = $this->zoneRepository->findOneBy(['name' => $codeOrName]);
        Assert::notNull(
            $zone,
            'Zone does not exist.',
        );

        return $zone;
    }

    /**
     * @Transform /^rest of the world$/
     */
    public function getRestOfTheWorldZone(): ZoneInterface
    {
        $zone = $this->zoneRepository->findOneBy(['code' => 'RoW']);
        Assert::notNull(
            $zone,
            'Rest of the world zone does not exist.',
        );

        return $zone;
    }
}
