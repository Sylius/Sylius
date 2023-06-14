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

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ManagingProductsContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private RepositoryInterface $productRepository,
        private RepositoryInterface $productVariantRepository,
        private RepositoryInterface $productReviewRepository,
    ) {
    }

    /**
     * @When /^I delete the ("[^"]+" variant of product "[^"]+")$/
     */
    public function iDeleteTheVariantOfProduct(ProductVariantInterface $productVariant)
    {
        $this->productVariantRepository->remove($productVariant);
    }

    /**
     * @When /^I try to delete the ("[^"]+" variant of product "[^"]+")$/
     */
    public function iTryToDeleteTheVariantOfProduct(ProductVariantInterface $productVariant)
    {
        try {
            $this->productVariantRepository->remove($productVariant);
            /** @phpstan-ignore-next-line  */
        } catch (\Doctrine\DBAL\DBALException|\Doctrine\DBAL\Exception $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @When /^I delete the ("[^"]+" product)$/
     */
    public function iDeleteTheProduct(ProductInterface $product)
    {
        $this->productRepository->remove($product);
    }

    /**
     * @When /^I try to delete the ("[^"]+" product)$/
     */
    public function iTryToDeleteTheProduct(ProductInterface $product)
    {
        try {
            $this->productRepository->remove($product);
        } catch (\Exception $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @Then /^I should be notified that this (?:variant|product) is in use and cannot be deleted$/
     */
    public function iShouldBeNotifiedThatThisProductVariantIsInUseAndCannotBeDeleted()
    {
        if (class_exists('\Doctrine\DBAL\DBALException')) {
            Assert::isInstanceOf($this->sharedStorage->get('last_exception'), \Doctrine\DBAL\DBALException::class);
        }

        if (class_exists('\Doctrine\DBAL\Exception')) {
            Assert::isInstanceOf($this->sharedStorage->get('last_exception'), \Doctrine\DBAL\Exception::class);
        }
    }

    /**
     * @Then /^(this variant) should not exist in the product catalog$/
     */
    public function productVariantShouldNotExistInTheProductCatalog(ProductVariantInterface $productVariant)
    {
        Assert::null($this->productVariantRepository->findOneBy(['code' => $productVariant->getCode()]));
    }

    /**
     * @Then /^(this variant) should still exist in the product catalog$/
     */
    public function productVariantShouldExistInTheProductCatalog(ProductVariantInterface $productVariant)
    {
        Assert::notNull($productVariant);
    }

    /**
     * @Then /^(this product) should still exist in the product catalog$/
     */
    public function productShouldExistInTheProductCatalog(ProductInterface $product)
    {
        Assert::notNull($product);
    }

    /**
     * @Then /^there should be no reviews of (this product)$/
     */
    public function thereAreNoProductReviews(ProductInterface $product)
    {
        $reviews = $this->productReviewRepository->findBy(['reviewSubject' => $product]);

        Assert::same($reviews, []);
    }

    /**
     * @Then /^there should be no variants of (this product) in the product catalog$/
     */
    public function thereAreNoVariants(ProductInterface $product)
    {
        $variants = $this->productVariantRepository->findBy(['product' => $product]);

        Assert::same($variants, []);
    }
}
