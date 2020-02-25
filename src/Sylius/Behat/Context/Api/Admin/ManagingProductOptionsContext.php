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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Webmozart\Assert\Assert;

final class ManagingProductOptionsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(ApiClientInterface $client, SharedStorageInterface $sharedStorage)
    {
        $this->client = $client;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I browse product options
     */
    public function iBrowseProductOptions(): void
    {
        $this->client->index('product_options');
    }

    /**
     * @Given I want to create a new product option
     */
    public function iWantToCreateANewProductOption(): void
    {
        $this->client->buildCreateRequest('product_options');
    }

    /**
     * @Given I want to modify the :productOption product option
     */
    public function iWantToModifyProductOption(ProductOptionInterface $productOption): void
    {
        $this->sharedStorage->set('product_option', $productOption);
        $this->client->buildUpdateRequest('product_options', $productOption->getCode());
    }

    /**
     * @When I name it :name in :language
     */
    public function iNameItInLanguage(string $name, string $language): void
    {
        $this->client->addCompoundRequestData(['translations' => [$language => ['name' => $name, 'locale' => $language]]]);
    }

    /**
     * @When I rename it to :name in :language
     */
    public function iRenameItInLanguage(string $name, string $language): void
    {
        $this->client->updateRequestData(['translations' => [$language => ['name' => $name, 'locale' => $language]]]);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs(string $code): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I add the :value option value identified by :code
     */
    public function iAddTheOptionValueWithCodeAndValue(string $value, string $code): void
    {
        $this->client->addCompoundRequestData([
            'values' => [
                ['code' => $code, 'translations' => [['value' => $value, 'locale' => 'en_US']]]
            ]
        ]);
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
        $itemsCount = $this->client->countCollectionItems();

        Assert::eq($count, $itemsCount, sprintf('Expected %d product options, but got %d', $count, $itemsCount));
    }

    /**
     * @Then the product option :productOption should be in the registry
     * @Then the product option :productOption should appear in the registry
     */
    public function theProductOptionShouldAppearInTheRegistry(ProductOptionInterface $productOption): void
    {
        $this->sharedStorage->set('product_option', $productOption);

        $this->client->index('product_options');
        Assert::true($this->client->hasItemWithValue('name', $productOption->getName()));
    }

    /**
     * @Then /^(this product option) name should be "([^"]+)"$/
     */
    public function thisProductOptionNameShouldBe(ProductOptionInterface $productOption, string $name): void
    {
        $this->client->show('product_options', $productOption->getCode());
        Assert::true($this->client->hasValue('name', $name));
    }

    /**
     * @Then /^(product option "[^"]+") should have the "([^"]+)" option value$/
     */
    public function thisProductOptionShouldHaveTheOptionValue(
        ProductOptionInterface $productOption,
        string $optionValueCode
    ): void {
        $this->client->subResourceIndex('product_options', 'values', $productOption->getCode());

        Assert::true($this->client->hasItemWithValue('code', $optionValueCode));
    }
}
