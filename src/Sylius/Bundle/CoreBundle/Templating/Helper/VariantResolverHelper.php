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

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class VariantResolverHelper extends Helper
{
    /**
     * @var ProductVariantResolverInterface
     */
    private $productVariantResolver;

    /**
     * @param ProductVariantResolverInterface $productVariantResolver
     */
    public function __construct(ProductVariantResolverInterface $productVariantResolver)
    {
        $this->productVariantResolver = $productVariantResolver;
    }

    /**
     * @param ProductInterface $product
     *
     * @return ProductVariantInterface
     */
    public function resolveVariant(ProductInterface $product)
    {
        return $this->productVariantResolver->getVariant($product);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_resolve_variant';
    }
}
