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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductVariantsMapExtension extends AbstractExtension
{
    public function __construct(private ProductVariantsMapProviderInterface $productVariantsMapProvider)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_product_variants_map', [$this->productVariantsMapProvider, 'provide']),
        ];
    }
}
