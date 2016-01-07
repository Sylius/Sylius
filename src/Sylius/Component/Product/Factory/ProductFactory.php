<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Product\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class ProductFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $variantFactory;

    /**
     * @var FactoryInterface
     */
    private $translatableFactory;

    /**
     * @param FactoryInterface $translatableResourceFactory
     * @param FactoryInterface $variantFactory
     */
    public function __construct(
        FactoryInterface $translatableResourceFactory,
        FactoryInterface $variantFactory
    ) {
        $this->translatableFactory = $translatableResourceFactory;
        $this->variantFactory = $variantFactory;
    }

    /**
     * (@inheritdoc}
     */
    public function createNew()
    {
        $variant = $this->variantFactory->createNew();
        $variant->setMaster(true);

        $product = $this->translatableFactory->createNew();
        $product->setMasterVariant($variant);

        return $product;
    }
}
