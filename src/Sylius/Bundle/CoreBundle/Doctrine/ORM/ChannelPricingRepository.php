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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Repository\ChannelPricingRepositoryInterface;

class ChannelPricingRepository extends EntityRepository implements ChannelPricingRepositoryInterface
{
    public function findWithDiscountedPrice(): array
    {
        return $this->createQueryBuilder('channelPricing')
            ->innerJoin('channelPricing.appliedPromotions', 'catalogPromotion')
            ->addSelect('COUNT(catalogPromotion.id) as HIDDEN numberOfCatalogPromotion')
            ->groupBy('channelPricing')
            ->having('numberOfCatalogPromotion > 0')
            ->getQuery()
            ->getResult()
        ;
    }
}
