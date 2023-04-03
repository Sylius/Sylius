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

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsMapHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductVariantsMapExtension extends AbstractExtension
{
    public function __construct(private ProductVariantsMapHelper $productVariantsMapHelper)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_product_variants_map', [$this->productVariantsMapHelper, 'getMap']),
        ];
    }
}
