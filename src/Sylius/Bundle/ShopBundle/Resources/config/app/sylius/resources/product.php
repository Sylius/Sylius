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

use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Operations;
use Sylius\Resource\Metadata\ResourceMetadata;
use Sylius\Resource\Metadata\Show;

return (new ResourceMetadata())
    ->withClass('%sylius.model.product.class%')
    ->withSection('shop')
    ->withRoutePrefix('/{_locale<%sylius_shop.locale_regex%>}')
    ->withTemplatesDir('@SyliusShop/product')
    ->withRouteCondition("not context.isSyliusRoutingBcLayerEnabled('shop_product')")
    ->withOperations(new Operations([
        new Index(
            path: '/taxons/{slug}',
            routeName: '_sylius_shop_product_index',
            routeRequirements: [
                'slug' => '.+(?<!/)',
            ],
            grid: 'sylius_shop_product',
        ),
        new Show(
            path: '/products/{slug}',
            routeName: '_sylius_shop_product_show',
            repositoryMethod: 'findOneByChannelAndSlug',
            repositoryArguments: [
                '@=sylius_context_shopper.getChannel()',
                '@=sylius_context_shopper.getLocaleCode()',
                '@=request.attributes.get(\'slug\')',
            ],
        ),
    ]))
;
