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

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProvider;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. Use {@see ProductVariantsMapProvider} instead. */
class ProductVariantsPricesHelper extends Helper
{
    public function __construct(private ProductVariantsPricesProviderInterface $productVariantsPricesProvider)
    {
        trigger_deprecation(
            'sylius/core-bundle',
            '1.13',
            'The "%s" class is deprecated and will be removed in Sylius 2.0. Use "%s" instead.',
            self::class,
            ProductVariantsMapProvider::class,
        );
    }

    public function getPrices(ProductInterface $product, ChannelInterface $channel): array
    {
        return $this->productVariantsPricesProvider->provideVariantsPrices($product, $channel);
    }

    public function getName(): string
    {
        return 'sylius_product_variants_prices';
    }
}
