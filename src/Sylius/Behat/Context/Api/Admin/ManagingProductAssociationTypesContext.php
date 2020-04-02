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
use Sylius\Component\Product\Model\ProductAssociationType;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Webmozart\Assert\Assert;

final class ManagingProductAssociationTypesContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        SerializerInterface $serializer
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->serializer = $serializer;
    }

    /**
     * @When I want to create a new product association type
     */
    public function iWantToCreateANewProductAssociationType(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I specify its code as :productAssociationTypeCode
     */
    public function iSpecifyItsCodeAs($productAssociationTypeCode): void
    {
        $this->client->addRequestData('code', $productAssociationTypeCode);
    }

    /**
     * @When I name it :productAssociationTypeName in :localeCode
     */
    public function iNameItIn(string $productAssociationTypeName, string $localeCode): void
    {
        $this->client->updateRequestData(['translations' => [$localeCode => ['name' => $productAssociationTypeName, 'locale' => $localeCode]]]);
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
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Product aAssociation type could not be created'
        );
    }

    /**
     * @Then the product association type :name should appear in the store
     */
    public function theProductAssociationTypeShouldAppearInTheStore(string $Name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $Name),
            sprintf('There is no product association type with name "%s"', $Name)
        );
    }

    /**
     * @When I browse product association types
     * @When I want to browse product association types
     */
    public function iBrowseProductAssociationTypes(): void
    {
        $this->client->index();
    }

    /**
     * @Then I should see :count product association types in the list
     * @Then I should see a single product association type in the list
     */
    public function iShouldSeeProductAssociationTypesInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index()), $count);
    }

    /**
     * @Then I should see the product association type :name in the list
     */
    public function iShouldSeeTheProductAssociationTypeInTheList(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $name),
            sprintf('There is no product association type with name "%s"', $name)
        );
    }

    /**
     * @When I delete the :productAssociationType product association type
     */
    public function iDeleteTheProductAssociationType(ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->sharedStorage->set('product_association_type_code', $productAssociationType->getCode());
        $this->client->delete($productAssociationType->getCode());
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     * @Then I should be notified that they have been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true($this->responseChecker->isDeletionSuccessful(
            $this->client->getLastResponse()),
            'Product association type could not be deleted'
        );
    }

    /**
     * @Then this product association type should no longer exist in the registry
     */
    public function thisProductAssociationTypeShouldNoLongerExistInTheRegistry(): void
    {
        $code = $this->sharedStorage->get('product_association_type_code');
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'code', $code),
            sprintf('Product association type with code %s exist', $code)
        );
    }

    /**
     * @When I want to modify the :productAssociationType product association type
     */
    public function iWantToModifyTheProductAssociationType(ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->client->buildUpdateRequest($productAssociationType->getCode());
    }

    /**
     * @When I rename it to :name in :language
     */
    public function iRenameItToIn(string $name, string $language): void
    {
        $this->client->updateRequestData(['translations' => [$language => ['name' => $name, 'locale' => $language]]]);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Product association type could not be edited'
        );
    }

    /**
     * @Then this product association type name should be :name
     */
    public function thisProductAssociationTypeNameShouldBe(string $name): void
    {
        /** @var ProductAssociationType $productAssociationType */
        $productAssociationType = $this->sharedStorage->get('product_association_type');
        Assert::true(
            $this->responseChecker->hasValue($this->client->show($productAssociationType->getCode()), 'name', $name),
            sprintf('Product association type name is not %s', $name)
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        /** @var ProductAssociationType $productAssociationType */
        $productAssociationType = $this->sharedStorage->get('product_association_type');

        $productAssociationTypeSerialised = $this->serializer->serialize($productAssociationType, 'json', ['groups' => 'product_association_type:update']);
        Assert::keyNotExists(\json_decode($productAssociationTypeSerialised, true), 'code');
    }

    /**
     * @When I check the :productAssociationType product association type
     * @When I check also the :productAssociationType product association type
     */
    public function iCheckTheProductAssociationType(ProductAssociationTypeInterface $productAssociationType): void
    {
        $productAssociationTypeToDelete = [];
        if ($this->sharedStorage->has('product_association_type_to_delete')) {
            $productAssociationTypeToDelete = $this->sharedStorage->get('product_association_type_to_delete');
        }
        $productAssociationTypeToDelete[] = $productAssociationType->getCode();
        $this->sharedStorage->set('product_association_type_to_delete', $productAssociationTypeToDelete);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        foreach ($this->sharedStorage->get('product_association_type_to_delete') as $code) {
            $this->client->delete($code);
        }
    }

    /**
     * @When I filter product association types with code containing :value
     */
    public function iFilterProductAssociationTypesWithCodeContaining(string $value)
    {
        $this->client->addFilter('code', $value);
        $this->client->filter();
    }

    /**
     * @When I filter product association types with name containing :value
     */
    public function iFilterProductAssociationTypesWithNameContaining(string $value)
    {
        $this->client->addFilter('translations.name', $value);
        $this->client->filter();
    }

    /**
     * @Then I should see only one product association type in the list
     */
    public function iShouldSeeOnlyOneProductAssociationTypeInTheList()
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), 1);
    }
}
