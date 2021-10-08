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

namespace Sylius\Bundle\PromotionBundle\Criteria;

use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Provider\DateTimeProviderInterface;

final class DateRange implements CriteriaInterface
{
    private DateTimeProviderInterface $calendar;

    public function __construct(DateTimeProviderInterface $calendar)
    {
        $this->calendar = $calendar;
    }

    public function meets(array $catalogPromotions): array
    {
        $now = $this->calendar->now();
        /** @var CatalogPromotionInterface $catalogPromotion */
        foreach ($catalogPromotions as $key => $catalogPromotion) {
            $startDate = $catalogPromotion->getStartDate();
            $endDate = $catalogPromotion->getEndDate();

            if (
                ($startDate !== null && $startDate > $now) ||
                ($endDate !== null && $endDate < $now)
            ) {
                unset($catalogPromotions[$key]);
            }
        }

        return $catalogPromotions;
    }
}
