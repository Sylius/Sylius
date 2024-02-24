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
use Ramsey\Uuid\Uuid;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Webmozart\Assert\Assert;

final class ManagingProductAttributesContext implements Context
{
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I want to see all product attributes in store
     */
    public function iWantToBrowseProductAttributes(): void
    {
        $this->client->index(Resources::PRODUCT_ATTRIBUTES);
    }

    /**
     * @When /^I(?:| try to) delete (this product attribute)$/
     */
    public function iDeleteThisProductAttribute(ProductAttributeInterface $attribute): void
    {
        $this->client->delete(Resources::PRODUCT_ATTRIBUTES, $attribute->getCode());
    }

    /**
     * @When I want to create a new :type product attribute
     */
    public function iWantToCreateANewTypedProductAttribute(string $type): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_ATTRIBUTES);
        $this->client->addRequestData('type', $type);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I name it :name in :localeCode
     * @When I change its name to :name in :localeCode
     * @When I do not name it
     * @When I remove its name from :localeCode translation
     */
    public function iNameItIn(string $name = '', string $localeCode = 'en_US'): void
    {
        $this->client->updateRequestData(['translations' => [$localeCode => ['name' => $name]]]);
    }

    /**
     * @When I (also) add value :value in :localeCode
     */
    public function iAddValueIn(string $value, string $localeCode): void
    {
        $uuid = Uuid::uuid4()->toString();

        $this->client->addRequestData('configuration', ['choices' => [$uuid => [$localeCode => $value]]]);
    }

    /**
     * @When I disable its translatability
     */
    public function iDisableItsTranslatability(): void
    {
        $this->client->addRequestData('translatable', false);
    }

    /**
     * @When I check multiple option
     */
    public function iCheckMultipleOption(): void
    {
        $this->client->addRequestData('configuration', ['multiple' => true]);
    }

    /**
     * @When I do not check multiple option
     * @When I do not specify its code
     */
    public function intentionallyBlank(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I specify its :limitType entries value as :count
     * @When I specify its :limitType length as :count
     */
    public function iSpecifyItsLimitTypeEntriesAs(string $limitType, int $count): void
    {
        $this->client->addRequestData('configuration', [$limitType => $count]);
    }

    /**
     * @When /^I want to edit (this product attribute)$/
     */
    public function iWantToEditThisProductAttribute(ProductAttributeInterface $productAttribute): void
    {
        $this->sharedStorage->set('product_attribute', $productAttribute);

        $this->client->buildUpdateRequest(Resources::PRODUCT_ATTRIBUTES, $productAttribute->getCode());
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
     * @When /^I change (its) value "([^"]+)" to "([^"]+)"$/
     */
    public function iChangeItsValueTo(
        ProductAttributeInterface $productAttribute,
        string $oldValue,
        string $newValue,
    ): void {
        $response = $this->client->show(Resources::PRODUCT_ATTRIBUTES, $productAttribute->getCode());
        $configuration = $this->responseChecker->getValue($response, 'configuration');

        $choices = $configuration['choices'];
        foreach ($choices as $key => $choice) {
            if ($choice['en_US'] === $oldValue) {
                $choices[$key]['en_US'] = $newValue;

                break;
            }
        }

        $this->client->updateRequestData(['configuration' => ['choices' => $choices]]);
    }

    /**
     * @When I delete value :value
     */
    public function iDeleteValue(string $value): void
    {
        /** @var ProductAttributeInterface $productAttribute */
        $productAttribute = $this->sharedStorage->get('product_attribute');
        $response = $this->client->show(Resources::PRODUCT_ATTRIBUTES, $productAttribute->getCode());
        $configuration = $this->responseChecker->getValue($response, 'configuration');

        $choices = $configuration['choices'];
        foreach ($choices as $key => $choice) {
            if ($choice['en_US'] === $value) {
                unset($choices[$key]);

                break;
            }
        }

        $this->client->setRequestData(['configuration' => ['choices' => $choices]]);
    }

    /**
     * @Then I should see :count product attributes in the list
     */
    public function iShouldSeeCountProductAttributesInTheList(int $count): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then the first product attribute on the list should have name :name
     */
    public function theFirstProductAttributeOnTheListShouldHaveName(string $name): void
    {
        $first = $this->responseChecker->getCollection($this->client->getLastResponse())[0];

        Assert::same($first['translations']['en_US']['name'], $name);
    }

    /**
     * @Then the last product attribute on the list should have name :name
     */
    public function theLastProductAttributeOnTheListShouldHaveName(string $name): void
    {
        $collection = $this->responseChecker->getCollection($this->client->getLastResponse());
        $last = end($collection);

        Assert::same($last['translations']['en_US']['name'], $name);
    }

    /**
     * @Then I should see the product attribute :attributeName in the list
     */
    public function iShouldSeeTheProductAttributeInTheList(string $attributeName): void
    {
        Assert::true($this->responseChecker->hasItemWithTranslation(
            $this->client->getLastResponse(),
            'en_US',
            'name',
            $attributeName,
        ));
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse());
    }

    /**
     * @Then /^(this product attribute) should no longer exist in the registry$/
     */
    public function thisProductAttributeShouldNoLongerExistInTheRegistry(ProductAttributeInterface $productAttribute): void
    {
        $response = $this->client->index(Resources::PRODUCT_ATTRIBUTES);

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'code', $productAttribute->getCode()),
            sprintf('Product attribute with code %s exists, but should not', $productAttribute->getCode()),
        );
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot delete, the product attribute is in use.',
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Product attribute could not be created',
        );
    }

    /**
     * @Then the :type attribute :name should appear in the store
     * @Then the :type attribute :name should still be in the store
     */
    public function theAttributeShouldAppearInTheStore(string $type, string $name): void
    {
        $response = $this->client->index(Resources::PRODUCT_ATTRIBUTES);

        /** @var array<string, mixed> $item */
        foreach ($this->responseChecker->getCollection($response) as $item) {
            if ($item['type'] === $type && $item['translations']['en_US']['name'] === $name) {
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'Product attribute of type "%s" with name "%s" has not been found',
            $type,
            $name,
        ));
    }

    /**
     * @Then the attribute with :field :value should not appear in the store
     */
    public function theAttributeWithCodeShouldNotAppearInTheStore(string $field, string $value): void
    {
        $response = $this->client->index(Resources::PRODUCT_ATTRIBUTES);

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, $field, $value),
            sprintf('Product attribute with %s %s exists, but should not', $field, $value),
        );
    }

    /**
     * @Then I should see the value :value
     */
    public function iShouldSeeTheValue(string $value): void
    {
        $content = $this->responseChecker->getResponseContent($this->client->getLastResponse());
        $choices = $content['configuration']['choices'];

        foreach ($choices as $values) {
            if (in_array($value, $values)) {
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'Product attribute value "%s" has not been found in choices: %s',
            $value,
            json_encode($choices),
        ));
    }

    /**
     * @Then /^(this product attribute) should have value "([^"]+)"$/
     */
    public function thisProductAttributeShouldHaveValue(
        ProductAttributeInterface $productAttribute,
        string $value,
    ): void {
        $this->client->show(Resources::PRODUCT_ATTRIBUTES, $productAttribute->getCode());

        $this->iShouldSeeTheValue($value);
    }

    /**
     * @Then /^(this product attribute) should not have value "([^"]+)"$/
     */
    public function thisProductAttributeShouldNotHaveValue(
        ProductAttributeInterface $productAttribute,
        string $value,
    ): void {
        $response = $this->client->show(Resources::PRODUCT_ATTRIBUTES, $productAttribute->getCode());
        $content = $this->responseChecker->getResponseContent($response);
        $choices = $content['configuration']['choices'];

        foreach ($choices as $values) {
            if (in_array($value, $values)) {
                throw new \InvalidArgumentException(sprintf(
                    'Product attribute value "%s" has been found but should not',
                    $value,
                ));
            }
        }
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatFieldIsRequired(string $field): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Please enter attribute %s.', $field),
        );
    }

    /**
     * @Then I should be notified that product attribute with this code already exists
     */
    public function iShouldBeNotifiedThatProductAttributeWithThisCodeAlreadyExists(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'This code is already in use.',
        );
    }

    /**
     * @Then I should be notified that max length must be greater or equal to the min length
     */
    public function iShouldBeNotifiedThatMaxLengthMustBeGreaterOrEqualToTheMinLength(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Configuration max length must be greater or equal to the min length.',
        );
    }

    /**
     * @Then there should still be only one product attribute with code :code
     */
    public function thereShouldStillBeOnlyOneProductAttributeWithCode(string $code): void
    {
        $items = $this->responseChecker->getCollectionItemsWithValue(
            $this->client->index(Resources::PRODUCT_ATTRIBUTES),
            'code',
            $code,
        );

        Assert::count($items, 1, sprintf('More than one attribute with code %s found', $code));
    }

    /**
     * @Then I should be notified that max entries value must be greater or equal to the min entries value
     */
    public function iShouldBeNotifiedThatMaxEntriesValueMustBeGreaterOrEqualToTheMinEntriesValue(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Configuration max entries value must be greater or equal to the min entries value.',
        );
    }

    /**
     * @Then I should be notified that min entries value must be lower or equal to the number of added choices
     */
    public function iShouldBeNotifiedThatMinEntriesValueMustBeLowerOrEqualToTheNumberOfAddedChoices(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Configuration min entries value must be lower or equal to the number of added choices.',
        );
    }

    /**
     * @Then I should be notified that multiple must be true if min or max entries values are specified
     */
    public function iShouldBeNotifiedThatMultipleMustBeTrueIfMinOrMaxEntriesValuesAreSpecified(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Configuration multiple must be true if min or max entries values are specified.',
        );
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'),
            'The code field with value NEW_CODE exist',
        );
    }

    /**
     * @Then I should not be able to edit its type
     */
    public function iShouldNotBeAbleToEditItsType(): void
    {
        $this->client->updateRequestData(['type' => 'percent']);

        Assert::false(
            $this->responseChecker->hasValue($this->client->update(), 'type', 'percent'),
            'The product attribute has new type select set.',
        );
    }
}
