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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class CatalogPromotionRuleFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'catalog_promotion_rule';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('type')->cannotBeEmpty()->end()
                ->scalarNode('catalogPromotion')->cannotBeEmpty()->end()
                ->arrayNode('configuration')->scalarPrototype()->end()
            ->end()
        ;
    }
}
