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

namespace spec\Sylius\Component\Promotion\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

final class CatalogPromotionScopeSpec extends ObjectBehavior
{
    function it_is_a_catalog_promotion_scope(): void
    {
        $this->shouldImplement(CatalogPromotionScopeInterface::class);
    }

    function it_does_not_have_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function its_type_is_mutable(): void
    {
        $this->getType()->shouldReturn(null);
        $this->setType('type');
        $this->getType()->shouldReturn('type');
    }

    function its_configuration_is_mutable(): void
    {
        $this->getConfiguration()->shouldReturn([]);
        $this->setConfiguration(['value' => 500]);
        $this->getConfiguration()->shouldReturn(['value' => 500]);
    }

    function its_catalog_promotion_is_mutable(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->getCatalogPromotion()->shouldReturn(null);
        $this->setCatalogPromotion($catalogPromotion);
        $this->getCatalogPromotion()->shouldReturn($catalogPromotion);
    }
}
