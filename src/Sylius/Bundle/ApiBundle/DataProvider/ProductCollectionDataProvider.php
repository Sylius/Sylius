<?php

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Bundle\ApiBundle\DataProvider\Helpers\ProductDataProviderHelper;
use Sylius\Bundle\ApiBundle\Entity\Product\Product;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;


class ProductCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var CollectionDataProviderInterface
     */
    private $collectionDataProvider;

    public function __construct(CollectionDataProviderInterface $collectionDataProvider)
    {
        $this->collectionDataProvider = $collectionDataProvider;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $products = $this->collectionDataProvider->getCollection($resourceClass, $operationName, $context);

        /** @var Product $product */
        foreach ($products as &$product) {
            $product = ProductDataProviderHelper::setCustomPropertiesToProduct($product);
        }

        return $products;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, Product::class, true);
    }

}
