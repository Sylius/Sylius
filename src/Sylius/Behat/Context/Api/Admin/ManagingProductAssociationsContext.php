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
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Sylius\Component\Product\Repository\ProductAssociationTypeRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ManagingProductAssociationsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

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
     * @When I want to associate products
     */
    public function iWantToAssociateProducts(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When With the product association type :productAssociationType
     */
    public function WithTheProductAssociationType(string $productAssociationType): void
    {
        $this->client->addRequestData('type', '/new-api/admin/product-association-types/' . $productAssociationType);
    }

    /**
     * @When I want the product :productCode to be the source
     */
    public function iWantTheProductToBeTheSource(string $productCode): void
    {
        $this->client->addRequestData('owner', '/new-api/admin/products/' . $productCode);
    }

    /**
     * @When I want to associate the product :productCode
     */
    public function iWantToAssociateTheProduct(string $productCode): void
    {
        $this->client->addRequestData('associatedProducts', ['/new-api/admin/products/' . $productCode]);
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
     * @When /^I want to delete the association (.*)$/
     */
    public function iWantToDeleteTheAssociation(ProductAssociationInterface $productAssociation): void
    {
        $this->client->buildCreateRequest();
        $this->client->delete((string) $productAssociation->getId());
    }

    /**
     * @When /^I want to modify the association (.*)$/
     */
    public function iWantToModifyTheAssociation(ProductAssociationInterface $productAssociation): void
    {
        $this->client->buildUpdateRequest((string) $productAssociation->getId());
    }

    /**
     * @Then /^The association (.*.) should have (.*.) associate product$/
     */
    public function theAssociationShouldHaveAssociateProduct(ProductAssociationInterface $productAssociation, int $number): void
    {
        Assert::count($productAssociation->getAssociatedProducts(), $number);
    }

    /**
     * @Then /^We should find the association (.*.)$/
     */
    public function weShouldFindTheAssociation(ProductAssociationInterface $productAssociation): void
    {
        Assert::isInstanceOf($productAssociation, ProductAssociationInterface::class);
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
     * @Then I should be notified that product association with those informations already exist
     */
    public function iShouldBeNotifiedThatProductAssociationTypeWithThoseInformationsAlreadyExist(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Duplicate entry'
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
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->client->filter();
    }

    /**
     * @Then /^I want to add the filter ("[^"]+") (.*)$/
     *
     * @param ProductAssociationTypeInterface|ProductInterface $class
     */
    public function iWantToAddFilterAndValue(string $filter, $class): void
    {
        $this->client->addFilter($filter, $class->getId());
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
     * @When I delete the :productAssociation product association type
     */
    public function iDeleteTheProductAssociation(ProductAssociationInterface $productAssociation): void
    {
        $this->client->delete($productAssociation->getId());
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     * @Then I should be notified that they have been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true($this->responseChecker->isDeletionSuccessful(
            $this->client->getLastResponse()),
            'Product association could not be deleted'
        );
    }

    /**
     * @When I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Product association could not be edited'
        );
    }
}
