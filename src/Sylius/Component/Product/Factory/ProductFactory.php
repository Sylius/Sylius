<?php

namespace Sylius\Component\Product\Factory;

use Sylius\Component\Translation\Provider\LocaleProviderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Translation\Factory\TranslatableFactory;

class ProductFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $variantFactory;

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @var FactoryInterface
     */
    private $translatableFactory;

    /**
     * @param FactoryInterface $translatableResourceFactory
     * @param LocaleProviderInterface $localeProvider
     * @param FactoryInterface $variantFactory
     */
    public function __construct(
        FactoryInterface $translatableResourceFactory,
        LocaleProviderInterface $localeProvider,
        FactoryInterface $variantFactory
    ) {
        $this->localeProvider = $localeProvider;
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
