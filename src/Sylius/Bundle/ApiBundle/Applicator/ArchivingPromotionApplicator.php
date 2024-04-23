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

use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\PromotionInterface;

final class ArchivingPromotionApplicator implements ArchivingPromotionApplicatorInterface
{
    public function __construct(private DateTimeProviderInterface $calendar)
    {
    }

    public function archive(PromotionInterface $data): PromotionInterface
    {
        $data->setArchivedAt($this->calendar->now());

        return $data;
    }

    public function restore(PromotionInterface $data): PromotionInterface
    {
        $data->setArchivedAt(null);

        return $data;
    }
}
