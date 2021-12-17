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

namespace Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler;

use Sylius\Bundle\CoreBundle\Provider\VariantsProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetCatalogPromotionScopeTypesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $types = [];
        foreach ($container->findTaggedServiceIds('sylius.catalog_promotion.variants_provider') as $id => $attributes) {
            /** @var VariantsProviderInterface $provider */
            $provider = $container->get($id);
            $types[] = $provider->getType();
        }

        $container->setParameter('sylius.catalog_promotion.scopes', $types);
    }
}
