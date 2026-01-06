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

namespace Tests\Sylius\Bundle\PromotionBundle\Stub;

use Sylius\Bundle\PromotionBundle\Attribute\AsCatalogPromotionVariantChecker;

#[AsCatalogPromotionVariantChecker(type: 'custom', priority: 9)]
final class CatalogPromotionVariantCheckerStub
{
}
