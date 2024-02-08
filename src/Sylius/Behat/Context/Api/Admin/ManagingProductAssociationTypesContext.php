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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Product\Model\ProductAssociationTypeInterface;
use Webmozart\Assert\Assert;

final class ManagingProductAssociationTypesContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I want to create a new product association type
     */
    public function iWantToCreateANewProductAssociationType(): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_ASSOCIATION_TYPES);
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
     * @When I do not name it
     */
    public function iNameItIn(string $productAssociationTypeName = null, string $localeCode = 'en_US'): void
    {
        $this->client->updateRequestData([
            'translations' => [
                 $localeCode => [
                      'name' => $productAssociationTypeName,
                      'locale' => $localeCode,
                 ],
            ],
        ]);
    }

    /**
     * @When I (try to) add it
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
            'Product association type could not be created',
        );
    }

    /**
     * @Then the product association type :name should appear in the store
     */
    public function theProductAssociationTypeShouldAppearInTheStore(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::PRODUCT_ASSOCIATION_TYPES), 'name', $name),
            sprintf('There is no product association type with name "%s"', $name),
        );
    }

    /**
     * @When I browse product association types
     * @When I want to browse product association types
     */
    public function iBrowseProductAssociationTypes(): void
    {
        $this->client->index(Resources::PRODUCT_ASSOCIATION_TYPES);
    }

    /**
     * @Then I should see :count product association types in the list
     * @Then I should see a single product association type in the list
     */
    public function iShouldSeeProductAssociationTypesInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->index(Resources::PRODUCT_ASSOCIATION_TYPES)), $count);
    }

    /**
     * @Then I should see the product association type :name in the list
     * @Then this product association type should still be named :name
     */
    public function iShouldSeeTheProductAssociationTypeInTheList(string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::PRODUCT_ASSOCIATION_TYPES), 'name', $name),
            sprintf('There is no product association type with name "%s"', $name),
        );
    }

    /**
     * @When I delete the :productAssociationType product association type
     */
    public function iDeleteTheProductAssociationType(ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->client->delete(Resources::PRODUCT_ASSOCIATION_TYPES, $productAssociationType->getCode());
    }

    /**
     * @Then /^I should be notified that (?:it has|they have) been successfully deleted$/
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful(
                $this->client->getLastResponse(),
            ),
            'Product association type could not be deleted',
        );
    }

    /**
     * @Then /^(this product association type) should no longer exist in the registry$/
     */
    public function thisProductAssociationTypeShouldNoLongerExistInTheRegistry(ProductAssociationTypeInterface $productAssociationType): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::PRODUCT_ASSOCIATION_TYPES), 'code', $productAssociationType->getCode()),
            sprintf('Product association type with code %s exist', $productAssociationType->getCode()),
        );
    }

    /**
     * @When I want to modify the :productAssociationType product association type
     */
    public function iWantToModifyTheProductAssociationType(ProductAssociationTypeInterface $productAssociationType): void
    {
        $this->client->buildUpdateRequest(Resources::PRODUCT_ASSOCIATION_TYPES, $productAssociationType->getCode());
    }

    /**
     * @When I rename it to :name in :language
     */
    public function iRenameItToIn(string $name, string $language): void
    {
        $this->client->updateRequestData(['translations' => [$language => ['name' => $name, 'locale' => $language]]]);
    }

    /**
     * @When I (try to) save my changes
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
            'Product association type could not be edited',
        );
    }

    /**
     * @Then /^(this product association type) name should be "([^"]+)"$/
     */
    public function thisProductAssociationTypeNameShouldBe(ProductAssociationTypeInterface $productAssociationType, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasValue($this->client->show(Resources::PRODUCT_ASSOCIATION_TYPES, $productAssociationType->getCode()), 'name', $name),
            sprintf('Product association type name is not %s', $name),
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->addRequestData('code', 'NEW_CODE');

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'),
            'The shipping category code should not be changed to "NEW_CODE", but it is',
        );
    }

    /**
     * @When I check (also) the :productAssociationType product association type
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
            $this->client->delete(Resources::PRODUCT_ASSOCIATION_TYPES, $code);
        }
    }

    /**
     * @When I filter product association types with code containing :value
     */
    public function iFilterProductAssociationTypesWithCodeContaining(string $value): void
    {
        $this->client->addFilter('code', $value);
        $this->client->filter();
    }

    /**
     * @When I filter product association types with name containing :value
     */
    public function iFilterProductAssociationTypesWithNameContaining(string $value): void
    {
        $this->client->addFilter('translations.name', $value);
        $this->client->filter();
    }

    /**
     * @Then I should see only one product association type in the list
     */
    public function iShouldSeeOnlyOneProductAssociationTypeInTheList(): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), 1);
    }

    /**
     * @Then I should be notified that product association type with this code already exists
     */
    public function iShouldBeNotifiedThatProductAssociationTypeWithThisCodeAlreadyExists(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The association type with given code already exists.',
        );
    }

    /**
     * @Then there should still be only one product association type with a code :code
     */
    public function thereShouldStillBeOnlyOneProductAssociationTypeWithACode(string $code): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::PRODUCT_ASSOCIATION_TYPES), 'code', $code),
            1,
            sprintf('More then one Product association type have code %s.', $code),
        );
    }

    /**
     * @When I do not specify its code
     */
    public function iDoNotSpecifyItsCode(): void
    {
        // Intentionally left blank
    }

    /**
     * @Then I should be notified that :type is required
     */
    public function iShouldBeNotifiedThatCodeIsRequired(string $type): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Please enter association type %s.', $type),
        );
    }

    /**
     * @Then the product association type with :type :value should not be added
     */
    public function theProductAssociationTypeWithNameShouldNotBeAdded(string $type, string $value): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::PRODUCT_ASSOCIATION_TYPES), $type, $value),
            sprintf('Product association type with %s %s exist', $type, $value),
        );
    }

    /**
     * @When I remove its name from :localeCode translation
     */
    public function iRemoveItsNameFromTranslation(string $localeCode): void
    {
        $this->client->updateRequestData([
            'translations' => [
                $localeCode => [
                    'name' => null,
                    'locale' => $localeCode,
                ],
            ],
        ]);
    }
}
