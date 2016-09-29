<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\Templating\Helper\ProductVariantsPricesHelper;
use Sylius\Component\Core\Model\ProductInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductVariantsPricesExtension extends \Twig_Extension
{
    /**
     * @var ProductVariantsPricesHelper
     */
    private $productVariantsPricesHelper;

    /**
     * @param ProductVariantsPricesHelper $productVariantsPricesHelper
     */
    public function __construct(ProductVariantsPricesHelper $productVariantsPricesHelper)
    {
        $this->productVariantsPricesHelper = $productVariantsPricesHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('sylius_product_variant_prices', [$this, 'getVariantsPrices']),
        ];
    }

    /**
     * @param ProductInterface $product
     *
     * @return array
     */
    public function getVariantsPrices(ProductInterface $product)
    {
        return $this->productVariantsPricesHelper->getPrices($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_variant_prices';
    }
}
