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

namespace Sylius\Component\Addressing\Checker;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ZoneDeletionChecker implements ZoneDeletionCheckerInterface
{
    public function __construct(private RepositoryInterface $zoneMemberRepository)
    {
    }

    public function isDeletable(ZoneInterface $zone): bool
    {
        $zoneMember = $this->zoneMemberRepository->findOneBy(['code' => $zone->getCode()]);

        return null === $zoneMember;
    }
}
