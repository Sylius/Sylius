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

namespace Sylius\Tests\Functional\Repository;

use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class ProductRepositoryTest extends KernelTestCase
{
    /** @var ProductRepositoryInterface<ProductInterface> */
    private ProductRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->clear();

        /** @var ProductRepositoryInterface<ProductInterface> $repository */
        $repository = self::getContainer()->get('sylius.repository.product');
        $this->repository = $repository;
    }

    #[Test]
    public function it_hydrates_reviews_of_all_given_products_including_those_without_reviews(): void
    {
        $productWithReviews = $this->repository->findOneByCode('MUG_REVIEW_BEST');
        $productWithoutReviews = $this->repository->findOneByCode('MUG_REVIEW_GOOD');

        self::assertFalse($this->isInitialized($productWithReviews));
        self::assertFalse($this->isInitialized($productWithoutReviews));

        $this->repository->hydrateReviews([$productWithReviews, $productWithoutReviews]);

        self::assertTrue($this->isInitialized($productWithReviews));
        self::assertTrue($this->isInitialized($productWithoutReviews));
        self::assertCount(3, $productWithReviews->getReviews());
        self::assertCount(0, $productWithoutReviews->getReviews());
    }

    private function isInitialized(ProductInterface $product): bool
    {
        $reviews = $product->getReviews();
        self::assertInstanceOf(AbstractLazyCollection::class, $reviews);

        return $reviews->isInitialized();
    }

    private function loadFixtures(): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        $fixtureLoader->load([
            __DIR__ . '/../../DataFixtures/ORM/resources/product_reviews.yml',
        ], [], [], PurgeMode::createDeleteMode());
    }
}
