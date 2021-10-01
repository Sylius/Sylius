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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScope;

final class CatalogPromotionScopeSpec extends ObjectBehavior
{
    function it_implements_catalog_promotion_scope_interface(): void
    {
        $this->shouldImplement(CatalogPromotionScopeInterface::class);
    }

    function it_extends_base_catalog_promotion_scope(): void
    {
        $this->shouldHaveType(CatalogPromotionScope::class);
    }
}
