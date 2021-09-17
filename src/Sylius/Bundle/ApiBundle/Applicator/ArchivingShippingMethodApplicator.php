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

namespace Sylius\Bundle\ApiBundle\Applicator;

use Sylius\Bundle\ShippingBundle\Provider\DateTimeProvider;
use Sylius\Component\Core\Model\ShippingMethodInterface;

/** @experimental */
final class ArchivingShippingMethodApplicator implements ArchivingShippingMethodApplicatorInterface
{
    private DateTimeProvider $calendar;

    public function __construct(DateTimeProvider $calendar)
    {
        $this->calendar = $calendar;
    }

    public function archive(ShippingMethodInterface $data): ShippingMethodInterface
    {
        $data->setArchivedAt($this->calendar->today());

        return $data;
    }

    public function restore(ShippingMethodInterface $data): ShippingMethodInterface
    {
        $data->setArchivedAt(null);

        return $data;
    }
}
