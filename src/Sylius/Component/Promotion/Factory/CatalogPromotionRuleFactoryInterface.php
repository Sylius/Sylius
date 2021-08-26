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

namespace Sylius\Component\Promotion\Factory;

use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface CatalogPromotionRuleFactoryInterface extends FactoryInterface
{
    public function createWithData(
        string $type,
        CatalogPromotionInterface $catalogPromotion,
        array $configuration
    ): CatalogPromotionRuleInterface;
}
