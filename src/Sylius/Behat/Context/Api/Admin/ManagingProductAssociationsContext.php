<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Doctrine\Common\Collections\Collection;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Repository\ProductAssociationTypeRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class ManagingProductAssociationsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var ProductAssociationInterface */
    private $productAssocation;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
    }

    /**
     * @When I want to modify the :product product
     */
    public function iWantToModifyAProduct(ProductInterface $product): void
    {
        $this->client->buildCreateRequest();
        $this->client->addRequestData('owner', '/new-api/admin/products/' . $product->getCode());
    }

    /**
     * @When /^I (associate as .*) the (.*.product)$/
     * @When /^I (associate as .*) the (.*.products)$/
     */
    public function iAssociateProductsAsProductAssociation(ProductAssociationTypeInterface $productAssociationType, ...$products): void
    {
        $associatedProductsUri = [];
        if (null !== $this->productAssocation) {
            $associatedProductsUri = $this->getAssociatedProductsUri($this->productAssocation->getAssociatedProducts());
        }
        $this->client->addRequestData('type', '/new-api/admin/product-association-types/' . $productAssociationType->getCode());
        if (!is_array($products[0])) {
            $associatedProductsUri[] = '/new-api/admin/products/' . $products[0]->getCode();
            $this->client->addRequestData('associatedProducts', $associatedProductsUri);

            return;
        }

        foreach ($products[0] as $product) {
            $associatedProductsUri[] = '/new-api/admin/products/' . $product->getCode();
        }
        $this->client->addRequestData('associatedProducts', $associatedProductsUri);
    }

    /**
     * @When I remove an associated product :product from :productAssociationType
     */
    public function iRemoveAnAssociatedProductFromProductAssociation(ProductInterface $product, ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->client->addRequestData('type', '/new-api/admin/product-association-types/' . $productAssociationType->getCode());
        $associatedProductsUri = $this->getAssociatedProductsUri($this->productAssocation->getAssociatedProducts());
        foreach ($associatedProductsUri as $key => $associatedProductUri) {
            if ('/new-api/admin/products/' . $product->getCode() !== $associatedProductUri) {
                $lastAssociationProductUri = $associatedProductUri;

                continue;
            }

            $associatedProductsUriKey = $key;
        }

        $associatedProductsUri[$associatedProductsUriKey] = $lastAssociationProductUri;

        $this->client->addRequestData('associatedProducts', $associatedProductsUri);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }


    /**
     * @When /^I want to modify an association for the (.*)$/
     */
    public function iWantToModifyTheAssociation(ProductAssociationInterface $productAssociation): void
    {
        $this->productAssocation = $productAssociation;
        $this->client->buildUpdateRequest((string) $productAssociation->getId());
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Product association could not be created'
        );
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @When I browse product associations
     */
    public function iBrowseProductAssociations(): void
    {
        $this->client->index();
    }

    /**
     * @Then I should see :count product associations in the list
     * @Then I should see a single product association in the list
     */
    public function iShouldSeeProductAssociationsInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index()), $count);
    }

    /**
     * @Then I should see the product association :code in the list
     */
    public function iShouldSeeTheProductAssociationInTheList(string $code): void
    {
        Assert::contains(
            $this->client->index(),
            '\/new-api\/admin\/product-association-types\/blue'
        );
    }

    /**
     * @When I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::inArray(
            $this->client->getLastResponse()->getStatusCode(),
            [Response::HTTP_OK, Response::HTTP_CREATED],
            'Product association could not be edited'
        );
    }

    /**
     * @Then /^this product should have an (association .*.) with (product .*.)$/
     * @Then /^this product should have an (association .*.) with (products .*.)$/
     */
    public function theProductShouldHaveAnAssociationWithProducts(
        ProductAssociationTypeInterface $productAssociationType,
        ...$products
    ) {
        $lastResponseContentAsArray = json_decode($this->client->getLastResponse()->getContent(), true);
        Assert::contains($lastResponseContentAsArray['type'], $productAssociationType->getCode());
        if (!is_array($products[0])) {
            foreach ($lastResponseContentAsArray['associatedProducts'] as $associatedProductUri) {
                Assert::contains(
                    $associatedProductUri,
                    $products[0]->getCode(),
                    sprintf(
                        'This product should have an association %s with product %s.',
                        $productAssociationType->getName(),
                        $products[0]
                    )
                );
            }
            return;
        }

        foreach ($products[0] as $product) {
            Assert::inArray(
                '/new-api/admin/products/' . $product->getCode(),
                $lastResponseContentAsArray['associatedProducts'],
                sprintf(
                    'This product should have an association %s with product %s.',
                    $productAssociationType->getName(),
                    $product
                )
            );
        }
    }

    /**
     * @Then /^this product should not have an (association .*.) with (product .*.)$/
     * @Then /^this product should not have an (association .*.) with (products .*.)$/
     */
    public function theProductShouldNotHaveAnAssociationWithProduct(
        ProductAssociationTypeInterface $productAssociationType,
        ...$products
    ) {
        $lastResponseContentAsArray = json_decode($this->client->getLastResponse()->getContent(), true);
        Assert::contains($lastResponseContentAsArray['type'], $productAssociationType->getCode());
        if (!is_array($products[0])) {
            foreach ($lastResponseContentAsArray['associatedProducts'] as $associatedProductUri) {
                Assert::notContains(
                    $associatedProductUri,
                    $products[0]->getCode(),
                    sprintf(
                        'This product should have an association %s with product %s.',
                        $productAssociationType->getName(),
                        $products[0]
                    )
                );
            }
            return;
        }

        foreach ($products[0] as $product) {
            foreach ($lastResponseContentAsArray['associatedProducts'] as $associatedProductUri) {
                Assert::notContains(
                    '/new-api/admin/products/' . $product->getCode(),
                    $lastResponseContentAsArray['associatedProducts'],
                    sprintf(
                        'This product should have an association %s with product %s.',
                        $productAssociationType->getName(),
                        $product
                    )
                );
            }
        }
    }

    private function getAssociatedProductsUri(Collection $products): array
    {
        $associatedProductUris = [];
        foreach ($products as $product) {
            $associatedProductUris[] = '/new-api/admin/products/' . $product->getCode();
        }

        return $associatedProductUris;
    }
}
