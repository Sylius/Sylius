<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductVariantsPricesHelper extends Helper
{
    /**
     * @var ProductVariantsPricesProviderInterface
     */
    private $productVariantsPricesProvider;

    /**
     * @param ProductVariantsPricesProviderInterface $productVariantsPricesProvider
     */
    public function __construct(ProductVariantsPricesProviderInterface $productVariantsPricesProvider)
    {
        $this->productVariantsPricesProvider = $productVariantsPricesProvider;
    }

    /**
     * @param ProductInterface $product
     * @param ChannelInterface $channel
     *
     * @return array
     */
    public function getPrices(ProductInterface $product, ChannelInterface $channel)
    {
        return $this->productVariantsPricesProvider->provideVariantsPrices($product, $channel);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_variants_prices';
    }
}
