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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param ProductVariantRepositoryInterface $productVariantRepository
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * @When /^I delete the ("[^"]+" variant of product "[^"]+")$/
     */
    public function iDeleteTheVariantOfProduct(ProductVariantInterface $productVariant)
    {
        $this->sharedStorage->set('product_variant_id', $productVariant->getId());
        $this->productVariantRepository->remove($productVariant);
    }

    /**
     * @When /^I try to delete the ("[^"]+" variant of product "[^"]+")$/
     */
    public function iTryToDeleteTheVariantOfProduct(ProductVariantInterface $productVariant)
    {
        try {
            $this->productVariantRepository->remove($productVariant);
        } catch (DBALException $exception) {
            $this->sharedStorage->set('last_exception', $exception);
        }
    }

    /**
     * @When I should be notified that this variant is in use and cannot be deleted
     */
    public function iShouldBeNotifiedThatThisVariantIsInUseAndCannotBeDeleted()
    {
        expect($this->sharedStorage->get('last_exception'))->toHaveType(DBALException::class);
    }

    /**
     * @Then /^this variant should not exist in the product catalog$/
     */
    public function productVariantShouldNotExistInTheProductCatalog()
    {
        $productVariantId = $this->sharedStorage->get('product_variant_id');
        $productVariant = $this->productVariantRepository->find($productVariantId);

        expect($productVariant)->toBe(null);
    }

    /**
     * @Then /^([^"]+) should still exist in the product catalog$/
     */
    public function productVariantShouldExistInTheProductCatalog(ProductVariantInterface $productVariant)
    {
        $productVariant = $this->productVariantRepository->find($productVariant->getId());

        expect($productVariant)->toNotBe(null);
    }
}
