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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\Product;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<ProductInterface> */
final readonly class CollectionProvider implements ProviderInterface
{
    /**
     * @param ProviderInterface<ProductInterface> $decoratedCollectionProvider
     * @param ProductRepositoryInterface<ProductInterface> $productRepository
     */
    public function __construct(
        private ProviderInterface $decoratedCollectionProvider,
        private ProductRepositoryInterface $productRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        Assert::true(is_a($operation->getClass(), ProductInterface::class, true));
        Assert::isInstanceOf($operation, GetCollection::class);

        $products = $this->decoratedCollectionProvider->provide($operation, $uriVariables, $context);

        if (!is_iterable($products)) {
            return $products;
        }

        $productsWithReviewsToLoad = [];
        foreach ($products as $product) {
            $reviews = $product instanceof ProductInterface ? $product->getReviews() : null;

            if ($reviews instanceof AbstractLazyCollection && !$reviews->isInitialized()) {
                $productsWithReviewsToLoad[] = $product;
            }
        }

        if ([] !== $productsWithReviewsToLoad) {
            $this->loadReviews($productsWithReviewsToLoad);
        }

        return $products;
    }

    /**
     * Fetch-joins reviews so Doctrine initializes the collections of all given products in a single query.
     *
     * @param ProductInterface[] $products
     */
    private function loadReviews(array $products): void
    {
        Assert::isInstanceOf($this->productRepository, EntityRepository::class);

        $this->productRepository->createQueryBuilder('product')
            ->addSelect('review')
            ->leftJoin('product.reviews', 'review')
            ->andWhere('product IN (:products)')
            ->setParameter('products', $products)
            ->addOrderBy('review.id')
            ->getQuery()
            ->getResult()
        ;
    }
}
