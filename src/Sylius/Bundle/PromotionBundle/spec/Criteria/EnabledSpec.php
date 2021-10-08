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

namespace spec\Sylius\Bundle\PromotionBundle\Criteria;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class EnabledSpec extends ObjectBehavior
{
    function it_implements_criteria_interface(): void
    {
        $this->shouldImplement(CriteriaInterface::class);
    }
    function it_returns_only_enabled_catalog_promotions(
        CatalogPromotionInterface $enabledCatalogPromotion,
        CatalogPromotionInterface $disabledCatalogPromotion,
        CatalogPromotionInterface $anotherEnabledCatalogPromotion
    ): void {
        $enabledCatalogPromotion->isEnabled()->willReturn(true);
        $disabledCatalogPromotion->isEnabled()->willReturn(false);
        $anotherEnabledCatalogPromotion->isEnabled()->willReturn(true);

        $this
            ->meets([$enabledCatalogPromotion, $disabledCatalogPromotion, $anotherEnabledCatalogPromotion])
            ->shouldReturn([$enabledCatalogPromotion, $anotherEnabledCatalogPromotion])
        ;
    }
}
