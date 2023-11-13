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

namespace Sylius\Bundle\ApiBundle\Applicator;

use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\Clock\ClockInterface;

/** @experimental */
final class ArchivingShippingMethodApplicator implements ArchivingShippingMethodApplicatorInterface
{
    public function __construct(private ClockInterface $clock)
    {
    }

    public function archive(ShippingMethodInterface $data): ShippingMethodInterface
    {
        $data->setArchivedAt($this->clock->now());

        return $data;
    }

    public function restore(ShippingMethodInterface $data): ShippingMethodInterface
    {
        $data->setArchivedAt(null);

        return $data;
    }
}
