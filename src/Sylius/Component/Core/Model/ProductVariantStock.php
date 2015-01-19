<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Pricing\Calculators;
use Sylius\Component\Product\Model\Variant as BaseVariant;
use Sylius\Component\Variation\Model\VariantInterface as BaseVariantInterface;
use Sylius\Component\Inventory\Model\Stock as BaseStock;

/**
 * Sylius core inventory manager entity.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class ProductVariantStock extends BaseStock implements ProductVariantStockInterface
{
    /**
     * Product variant.
     *
     * @var ProductVariantInterface
     */
    protected $variant;

    /**
     * {@inheritdoc}
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariant(ProductVariantInterface $variant)
    {
        $this->variant = $variant;

        return $this;
    }
    
}
