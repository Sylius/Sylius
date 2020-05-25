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
use Sylius\Component\Product\Model\ProductOptionInterface;
use Webmozart\Assert\Assert;

final class ManagingProductOptionsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        ApiClientInterface $client,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage
    ) {
        $this->client = $client;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I browse product options
     */
    public function iBrowseProductOptions(): void
    {
        $this->client->index();
    }

    /**
     * @Given I want to create a new product option
     */
    public function iWantToCreateANewProductOption(): void
    {
        $this->client->buildCreateRequest();
    }

    /**
     * @When I want to modify the :productOption product option
     */
    public function iWantToModifyProductOption(ProductOptionInterface $productOption): void
    {
        $this->sharedStorage->set('product_option', $productOption);
        $this->client->buildUpdateRequest($productOption->getCode());
    }

    /**
     * @When I name it :name in :language
     * @When I do not name it
     */
    public function iNameItInLanguage(?string $name = null, ?string $language = 'en_US'): void
    {
        $data = ['translations' => [$language => ['locale' => $language]]];
        if ($name !== null) {
            $data['translations'][$language]['name'] = $name;
        }

        $this->client->updateRequestData($data);
    }

    /**
     * @When I rename it to :name in :language
     */
    public function iRenameItInLanguage(string $name, string $language): void
    {
        $this->client->updateRequestData(['translations' => [$language => ['name' => $name, 'locale' => $language]]]);
    }

    /**
     * @When I remove its name from :language translation
     */
    public function iRemoveItsNameFromTranslation(string $language): void
    {
        $this->client->updateRequestData(['translations' => [$language => ['name' => '', 'locale' => $language]]]);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        if ($code !== null) {
            $this->client->addRequestData('code', $code);
        }
    }

    /**
     * @When I add the :value option value identified by :code
     */
    public function iAddTheOptionValueWithCodeAndValue(string $value, string $code): void
    {
        $this->client->addSubResourceData(
            'values',
            ['code' => $code, 'translations' => ['en_US' => ['value' => $value, 'locale' => 'en_US']]]
        );
    }

    /**
     * @When I do not add an option value
     */
    public function iDoNotAddAnOptionValue(): void
    {
        // Intentionally left blank to fulfill context expectation
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
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->client->update();
    }

    /**
     * @Then I should see :count product options in the list
     */
    public function iShouldSeeProductOptionsInTheList(int $count): void
    {
        $itemsCount = $this->responseChecker->countCollectionItems($this->client->getLastResponse());

        Assert::eq($count, $itemsCount, sprintf('Expected %d product options, but got %d', $count, $itemsCount));
    }

    /**
     * @Then the product option :productOption should be in the registry
     * @Then the product option :productOption should appear in the registry
     */
    public function theProductOptionShouldAppearInTheRegistry(ProductOptionInterface $productOption): void
    {
        $this->sharedStorage->set('product_option', $productOption);

        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(), 'name', $productOption->getName()),
            sprintf('Product option should have name "%s", but it does not.', $productOption->getName())
        );
    }

    /**
     * @Then the first product option in the list should have :field :value
     */
    public function theFirstProductOptionInTheListShouldHave(string $field, string $value): void
    {
        Assert::true(
            $this->responseChecker->hasItemOnPositionWithValue($this->client->getLastResponse(), 0, $field, $value),
            sprintf('There should be product option with %s "%s" on position %d, but it does not.', $field, $value, 1)
        );
    }

    /**
     * @Then the last product option in the list should have :field :value
     */
    public function theLastProductOptionInTheListShouldHave(string $field, string $value): void
    {
        $count = $this->responseChecker->countCollectionItems($this->client->getLastResponse());

        Assert::true(
            $this->responseChecker->hasItemOnPositionWithValue($this->client->getLastResponse(), $count - 1, $field, $value),
            sprintf('There should be product option with %s "%s" on position %d, but it does not.', $field, $value, $count - 1)
        );
    }

    /**
     * @Then the product option with :element :value should not be added
     */
    public function theProductOptionWithElementValueShouldNotBeAdded(string $element, string $value): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(), $element, $value),
            sprintf('Product option should not have %s "%s", but it does,', $element, $value)
        );
    }

    /**
     * @Then there should still be only one product option with :element :value
     */
    public function thereShouldStillBeOnlyOneProductOptionWith(string $element, string $value): void
    {
        $response = $this->client->index();
        $itemsCount = $this->responseChecker->countCollectionItems($response);

        Assert::same($itemsCount, 1, sprintf('Expected 1 product options, but got %d', $itemsCount));
        Assert::true($this->responseChecker->hasItemWithValue($response, $element, $value));
    }

    /**
     * @Then /^(this product option) name should be "([^"]+)"$/
     * @Then /^(this product option) should still be named "([^"]+)"$/
     */
    public function thisProductOptionNameShouldBe(ProductOptionInterface $productOption, string $name): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->show($productOption->getCode()), 'name', $name));
    }

    /**
     * @Then /^(product option "[^"]+") should have the "([^"]+)" option value$/
     * @Then /^(this product option) should have the "([^"]*)" option value$/
     */
    public function productOptionShouldHaveTheOptionValue(
        ProductOptionInterface $productOption,
        string $optionValueName
    ): void {
        Assert::true($this->responseChecker->hasItemWithTranslation(
            $this->client->subResourceIndex('values', $productOption->getCode()),
            'en_US',
            'value',
            $optionValueName
        ));
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false($this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'));
    }

    /**
     * @Then I should be notified that product option with this code already exists
     */
    public function iShouldBeNotifiedThatProductOptionWithThisCodeAlreadyExists(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Product option has been created successfully, but it should not'
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'code: The option with given code already exists.'
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s: Please enter option %s.', $element, $element)
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Product option could not be created'
        );
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        Assert::true(
            $this->responseChecker->isUpdateSuccessful($this->client->getLastResponse()),
            'Product option could not be edited'
        );
    }
}
