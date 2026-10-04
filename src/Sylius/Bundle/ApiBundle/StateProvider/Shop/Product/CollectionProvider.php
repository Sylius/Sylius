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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<ProductInterface> */
final readonly class CollectionProvider implements ProviderInterface
{
    /** @param ProviderInterface<ProductInterface> $decoratedCollectionProvider */
    public function __construct(
        private ProviderInterface $decoratedCollectionProvider,
        private ManagerRegistry $managerRegistry,
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
            $this->loadReviews($operation->getClass(), $productsWithReviewsToLoad);
        }

        return $products;
    }

    /**
     * Fetch-joins the reviews of all given products in a single query, so Doctrine initializes
     * their review collections at once instead of lazy-loading them one product at a time.
     *
     * @param class-string<ProductInterface> $productClass
     * @param ProductInterface[] $products
     */
    private function loadReviews(string $productClass, array $products): void
    {
        $entityManager = $this->managerRegistry->getManagerForClass($productClass);
        Assert::isInstanceOf($entityManager, EntityManagerInterface::class);

        $entityManager->createQueryBuilder()
            ->select('product', 'review')
            ->from($productClass, 'product')
            ->leftJoin('product.reviews', 'review')
            ->andWhere('product IN (:products)')
            ->setParameter('products', $products)
            ->getQuery()
            ->getResult()
        ;
    }
}
