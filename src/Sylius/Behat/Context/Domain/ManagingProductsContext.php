<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\DBAL\DBALException;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class ManagingProductsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * @var RepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @var RepositoryInterface
     */
    private $productReviewRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param RepositoryInterface $productRepository
     * @param RepositoryInterface $productVariantRepository
     * @param RepositoryInterface $productReviewRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $productRepository,
        RepositoryInterface $productVariantRepository,
        RepositoryInterface $productReviewRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->productReviewRepository = $productReviewRepository;
    }

    /**
     * @When /^I delete the ("[^"]+" variant of product "[^"]+")$/
     * @When /^I try to delete the ("[^"]+" variant of product "[^"]+")$/
     */
    public function iDeleteTheVariantOfProduct(ProductVariantInterface $productVariant)
    {
        try {
            $this->sharedStorage->set('product_variant_id', $productVariant->getId());
            $this->productVariantRepository->remove($productVariant);
        } catch (DBALException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @When /^I delete the ("[^"]+" product)$/
     * @When /^I try to delete the ("([^"]+)" product)$/
     */
    public function iDeleteTheProduct(ProductInterface $product)
    {
        try {
            $this->sharedStorage->set('product_id', $product->getId());
            $this->productRepository->remove($product);
        } catch (DBALException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @Then /^I should be notified that this (?:variant|product) is in use and cannot be deleted$/
     */
    public function iShouldBeNotifiedThatThisProductVariantIsInUseAndCannotBeDeleted()
    {
        Assert::isInstanceOf($this->sharedStorage->get('last_exception'), DBALException::class);
    }

    /**
     * @Then this variant should not exist in the product catalog
     */
    public function productVariantShouldNotExistInTheProductCatalog()
    {
        $productVariantId = $this->sharedStorage->get('product_variant_id');
        $productVariant = $this->productVariantRepository->find($productVariantId);

        Assert::null($productVariant);
    }

    /**
     * @Then this variant should still exist in the product catalog
     */
    public function productVariantShouldExistInTheProductCatalog()
    {
        $productVariantId = $this->sharedStorage->get('product_variant_id');
        $productVariant = $this->productVariantRepository->find($productVariantId);

        Assert::notNull($productVariant);
    }

    /**
     * @Then this product should still exist in the product catalog
     */
    public function productShouldExistInTheProductCatalog()
    {
        $productId = $this->sharedStorage->get('product_id');
        $product = $this->productRepository->find($productId);

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
