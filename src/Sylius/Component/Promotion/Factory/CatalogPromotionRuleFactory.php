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

final class CatalogPromotionRuleFactory implements CatalogPromotionRuleFactoryInterface
{
    private FactoryInterface $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function createNew(): CatalogPromotionRuleInterface
    {
        return $this->factory->createNew();
    }

    public function createWithData(
        string $type,
        CatalogPromotionInterface $catalogPromotion,
        array $configuration
    ): CatalogPromotionRuleInterface {
        $rule = $this->createNew();

        $rule->setType($type);
        $rule->setCatalogPromotion($catalogPromotion);
        $rule->setConfiguration($configuration);

        return $rule;
    }
}
