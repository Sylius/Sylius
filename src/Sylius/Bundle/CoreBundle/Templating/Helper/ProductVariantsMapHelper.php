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

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

class ProductVariantsMapHelper extends Helper
{
    public function __construct(private ProductVariantsMapProviderInterface $productVariantsMapProvider)
    {
    }

    public function getMap(ProductInterface $product, array $context): array
    {
        return $this->productVariantsMapProvider->provide($product, $context);
    }

    public function getName(): string
    {
        return 'sylius_product_variants_map';
    }
}
