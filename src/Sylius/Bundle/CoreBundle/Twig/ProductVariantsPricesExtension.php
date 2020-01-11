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

use Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsPricesHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ProductVariantsPricesExtension extends AbstractExtension
{
    /** @var ProductVariantsPricesHelper */
    private $productVariantsPricesHelper;

    public function __construct(ProductVariantsPricesHelper $productVariantsPricesHelper)
    {
        $this->productVariantsPricesHelper = $productVariantsPricesHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_product_variant_prices', [$this->productVariantsPricesHelper, 'getPrices']),
        ];
    }
}
