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

use ApiPlatform\Api\IriConverterInterface;
use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverterInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Webmozart\Assert\Assert;

final class ManagingProductVariantsContext implements Context
{
    use ValidationTrait;

    private const FIRST_COLLECTION_ITEM = 0;

    private const HUGE_NUMBER = 2147483647;

    public function __construct(
        private ProductVariantResolverInterface $variantResolver,
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
    ) {
    }

    /**
     * @When /^I want to create a new variant of (this product)$/
     */
    public function iWantToCreateANewProductVariant(ProductInterface $product): void
    {
        $this->client->buildCreateRequest(Resources::PRODUCT_VARIANTS);
        $this->client->addRequestData('product', $this->iriConverter->getIriFromResource($product));
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
     */
    public function iNameItIn(string $name, string $localeCode): void
    {
        $this->client->addRequestData('translations', [
            $localeCode => [
                'name' => $name,
            ],
        ]);
    }

    /**
     * @When /^I set its price to ("[^"]+") for ("[^"]+" channel)$/
     */
    public function iSetItsPriceToForChannel(int $price, ChannelInterface $channel): void
    {
        $this->client->addRequestData('channelPricings', [
            $channel->getCode() => [
                'price' => $price,
                'channelCode' => $channel->getCode(),
            ],
        ]);
    }

    /**
     * @When I set its price to a huge number for the :channel channel
     */
    public function iSetItsPriceToHugeNumberForTheChannel(ChannelInterface $channel): void
    {
        $this->iSetItsPriceToForChannel(self::HUGE_NUMBER, $channel);
    }

    /**
     * @When I set its original price to a huge number for the :channel channel
     */
    public function iSetItsOriginalPriceToHugeNumberForTheChannel(ChannelInterface $channel): void
    {
        $this->iSetItsOriginalPriceToForChannel(self::HUGE_NUMBER, $channel);
    }

    /**
     * @When I set its minimum price to a huge number for the :channel channel
     */
    public function iSetItsMinimumPriceAsOutOfRangeValueForChannel(ChannelInterface $channel): void
    {
        $this->iSetItsMinimumPriceToForChannel(self::HUGE_NUMBER, $channel);
    }

    /**
     * @When I remove its price from :channel channel
     */
    public function iRemoveItsPriceForChannel(ChannelInterface $channel): void
    {
        $content = $this->client->getContent();
        $content['channelPricings'][$channel->getCode()]['price'] = null;

        $this->client->setRequestData($content);
    }

    /**
     * @When I do not set its price
     * @When I do not specify its code
     * @When I do not set its :optionName option
     * @When I do not set its :firstOptionName and :secondOptionName options
     */
    public function iDoNotSetValue(): void
    {
        // Intentionally left blank
    }

    /**
     * @When I do not specify its current stock
     */
    public function iDoNotSpecifyItsCurrentStock(): void
    {
        $this->client->addRequestData('onHand', null);
    }

    /**
     * @When /^I set its original price to ("[^"]+") for ("[^"]+" channel)$/
     */
    public function iSetItsOriginalPriceToForChannel(int $originalPrice, ChannelInterface $channel): void
    {
        $this->client->addRequestData('channelPricings', [
            $channel->getCode() => [
                'originalPrice' => $originalPrice,
                'channelCode' => $channel->getCode(),
            ],
        ]);
    }

    /**
     * @When /^I set its minimum price to ("[^"]+") for ("[^"]+" channel)$/
     */
    public function iSetItsMinimumPriceToForChannel(int $minimumPrice, ChannelInterface $channel): void
    {
        $content = $this->client->getContent();
        $content['channelPricings'][$channel->getCode()]['minimumPrice'] = $minimumPrice;

        $this->client->updateRequestData($content);
    }

    /**
     * @When I( try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I want to modify the :variant product variant
     */
    public function iWantToModifyProductVariant(ProductVariantInterface $variant): void
    {
        $this->client->buildUpdateRequest(Resources::PRODUCT_VARIANTS, $variant->getCode());
    }

    /**
     * @When /^I change its price to ("[^"]+") for ("[^"]+" channel)$/
     */
    public function iChangeItsPriceToForChannel(int $originalPrice, ChannelInterface $channel): void
    {
        $this->client->addRequestData('channelPricings', [
            $channel->getCode() => [
                'price' => $originalPrice,
                'channelCode' => $channel->getCode(),
            ],
        ]);
    }

    /**
     * @When I set its :optionName option to :optionValue
     */
    public function iSetItsOptionAs(string $optionName, ProductOptionValueInterface $optionValue): void
    {
        $content = $this->client->getContent();
        $content['optionValues'][] = $this->sectionAwareIriConverter->getIriFromResourceInSection($optionValue, 'admin');

        $this->client->setRequestData($content);
    }

    /**
     * @When I change its :productOption option to :productOptionValue
     */
    public function iChangeItsOptionTo(
        ProductOptionInterface $productOption,
        ProductOptionValueInterface $productOptionValue,
    ): void {
        $content = $this->client->getContent();
        foreach ($content['optionValues'] as $key => $optionValueIri) {
            /** @var ProductOptionValueInterface $currentOptionValue */
            $currentOptionValue = $this->iriConverter->getResourceFromIri($optionValueIri);
            if ($currentOptionValue->getOptionCode() === $productOption->getCode()) {
                unset($content['optionValues'][$key]);
            }
        }

        $content['optionValues'][] = $this->iriConverter->getIriFromResource($productOptionValue);

        $this->client->setRequestData($content);
    }

    /**
     * @When I add additionally :productOptionValue value as :productOptionName option
     */
    public function iAddAdditionallyValueAsOption(
        ProductOptionValueInterface $productOptionValue,
        string $productOptionName,
    ): void {
        $content = $this->client->getContent();
        $content['optionValues'][] = $this->iriConverter->getIriFromResource($productOptionValue);

        $this->client->setRequestData($content);
    }

    /**
     * @When I set its shipping category as :shippingCategory
     */
    public function iSetItsShippingCategoryAs(ShippingCategoryInterface $shippingCategory): void
    {
        $this->client->addRequestData('shippingCategory', $this->iriConverter->getIriFromResource($shippingCategory));
    }

    /**
     * @When I do not want to have shipping required for this product variant
     */
    public function iDoNotWantToHaveShippingRequiredForThisProductVariant(): void
    {
        $this->client->addRequestData('shippingRequired', false);
    }

    /**
     * @When /^I want to view all variants of (this product)$/
     * @When /^I view(?:| all) variants of the (product "[^"]+")(?:| again)$/
     */
    public function iWantToViewAllVariantsOfThisProduct(ProductInterface $product): void
    {
        $this->client->index(Resources::PRODUCT_VARIANTS);
        $this->client->addFilter('product', $this->iriConverter->getIriFromResource($product));
        $this->client->addFilter('order[position]', 'asc');
        $this->client->filter();
    }

    /**
     * @When /^I delete the ("[^"]+" variant of product "[^"]+")$/
     * @When /^I try to delete the ("[^"]+" variant of product "[^"]+")$/
     */
    public function iDeleteTheVariantOfProduct(ProductVariantInterface $productVariant): void
    {
        $this->client->delete(Resources::PRODUCT_VARIANTS, $productVariant->getCode());
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->client->updateRequestData(['enabled' => false]);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->client->updateRequestData(['enabled' => true]);
    }

    /**
     * @When I disable its inventory tracking
     */
    public function iDisableItsTracking(): void
    {
        $this->client->updateRequestData(['tracked' => false]);
    }

    /**
     * @When I enable its inventory tracking
     */
    public function iEnableItsTracking(): void
    {
        $this->client->updateRequestData(['tracked' => true]);
    }

    /**
     * @When I set its height, width, depth and weight to :value
     */
    public function iSetItsDimensionsTo(float $value): void
    {
        $this->client->updateRequestData([
            'height' => $value,
            'width' => $value,
            'depth' => $value,
            'weight' => $value,
        ]);
    }

    /**
     * @When I change its quantity of inventory to :amount
     */
    public function iChangeItsQuantityOfInventoryTo(int $amount): void
    {
        $this->client->updateRequestData(['onHand' => $amount]);
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            sprintf(
                'Product Variant could not be created: %s',
                $this->responseChecker->getError($this->client->getLastResponse()),
            ),
        );
    }

    /**
     * @Then the :productVariantCode variant of the :productName product should appear in the store
     */
    public function theProductVariantShouldAppearInTheShop(string $productVariantCode, string $productName): void
    {
        $response = $this->client->index(Resources::PRODUCT_VARIANTS);

        Assert::true($this->responseChecker->hasItemWithValue($response, 'code', $productVariantCode));
    }

    /**
     * @Then the :productVariantCode variant of the :productName product should not appear in the store
     */
    public function theProductVariantShouldNotAppearInTheShop(string $productVariantCode, string $productName): void
    {
        $response = $this->client->index(Resources::PRODUCT_VARIANTS);

        Assert::false($this->responseChecker->hasItemWithValue($response, 'code', $productVariantCode));
    }

    /**
     * @Then /^the (?:variant with code "[^"]+") should be named "([^"]+)" in ("([^"]+)" locale)$/
     */
    public function theVariantWithCodeShouldBeNamedIn(string $name, string $localeCode): void
    {
        $response = $this->responseChecker->getCollection($this->client->index(Resources::PRODUCT_VARIANTS));

        $expectedTranslation = [
            'name' => $name,
        ];

        $translationInLocale = $response[self::FIRST_COLLECTION_ITEM]['translations'][$localeCode];

        Assert::allInArray(
            $expectedTranslation,
            $translationInLocale,
            sprintf('Expected translation %s, got %s', $expectedTranslation['name'], $translationInLocale['name']),
        );
    }

    /**
     * @Then /^the variant with code "([^"]+)" should be priced at ("[^"]+") for (channel "[^"]+")$/
     * @Then I should not have configured price for :channel channel
     */
    public function theVariantWithCodeShouldBePricedAtForChannel(
        ?string $variantCode = null,
        ?int $price = null,
        ?ChannelInterface $channel = null,
    ): void {
        $response = $this->responseChecker->getCollection($this->client->index(Resources::PRODUCT_VARIANTS));

        Assert::same($response[self::FIRST_COLLECTION_ITEM]['channelPricings'][$channel->getCode()]['price'], $price);
    }

    /**
     * @Then /^the variant with code "([^"]+)" should have an original price of ("[^"]+") for (channel "[^"]+")$/
     * @Then /^the variant with code "([^"]+)" should be originally priced at ("[^"]+") for (channel "[^"]+")$/
     */
    public function theVariantWithCodeShouldHaveAnOriginalPriceOfForChannel(
        ?string $variantCode,
        int $originalPrice,
        ChannelInterface $channel,
    ): void {
        $response = $this->responseChecker->getCollection($this->client->index(Resources::PRODUCT_VARIANTS));

        Assert::same($response[self::FIRST_COLLECTION_ITEM]['channelPricings'][$channel->getCode()]['originalPrice'], $originalPrice);
    }

    /**
     * @Then /^I should have original price equal to ("[^"]+") in ("[^"]+" channel)$/
     */
    public function iShouldHaveOriginalPriceEqualToInChannel(
        int $originalPrice,
        ChannelInterface $channel,
    ): void {
        $this->theVariantWithCodeShouldHaveAnOriginalPriceOfForChannel(null, $originalPrice, $channel);
    }

    /**
     * @Then /^the (variant with code "[^"]+") should have minimum price ("[^"]+") for (channel "([^"]+)")$/
     */
    public function theVariantWithCodeShouldHaveMinimumPriceForChannel(ProductVariantInterface $productVariant, int $minimumPrice, ChannelInterface $channel): void
    {
        $response = $this->responseChecker->getCollection($this->client->index(Resources::PRODUCT_VARIANTS));

        Assert::same($response[self::FIRST_COLLECTION_ITEM]['channelPricings'][$channel->getCode()]['minimumPrice'], $minimumPrice);
    }

    /**
     * @Then /^the (variant with code "[^"]+") should not have shipping required$/
     */
    public function theVariantWithCodeShouldNotHaveShippingRequired(ProductVariantInterface $productVariant): void
    {
        Assert::false($this->responseChecker->getValue($this->client->getLastResponse(), 'shippingRequired'));
    }

    /**
     * @Then I should see :amount variant(s) in the list
     */
    public function iShouldSeeNumberOfProductVariantsInTheList(int $amount): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), $amount);
    }

    /**
     * @Then I should see that the :productVariant variant is not tracked
     */
    public function iShouldSeeThatVariantIsNotTracked(ProductVariantInterface $productVariant): void
    {
        Assert::true($this->responseChecker->hasItemWithValues(
            $this->client->getLastResponse(),
            ['code' => $productVariant->getCode(), 'tracked' => false],
        ));
    }

    /**
     * @Then I should see that the :productVariant variant has zero on hand quantity
     */
    public function iShouldSeeThatTheVariantHasZeroOnHandQuantity(ProductVariantInterface $productVariant): void
    {
        Assert::true($this->responseChecker->hasItemWithValues(
            $this->client->getLastResponse(),
            ['code' => $productVariant->getCode(), 'onHand' => 0],
        ));
    }

    /**
     * @Then I should see that the :productVariant variant is enabled
     */
    public function iShouldSeeThatTheVariantIsEnabled(ProductVariantInterface $productVariant): void
    {
        Assert::true($this->responseChecker->hasItemWithValues(
            $this->client->getLastResponse(),
            ['code' => $productVariant->getCode(), 'enabled' => true],
        ));
    }

    /**
     * @Then this variant should be disabled
     */
    public function thisVariantShouldBeDisabled(): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'enabled', false));
    }

    /**
     * @Then this variant should be enabled
     */
    public function thisVariantShouldBeEnabled(): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'enabled', true));
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Product variant could not be deleted',
        );
    }

    /**
     * @Then /^(this variant) should not exist in the product catalog$/
     */
    public function thisProductVariantShouldNotExistInTheProductCatalog(ProductVariantInterface $productVariant): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue(
                $this->client->index(Resources::PRODUCT_VARIANTS),
                'code',
                $productVariant->getCode(),
            ),
            'The product variant still exists, but it should not',
        );
    }

    /**
     * @Then /^(this variant) should still exist in the product catalog$/
     */
    public function thisProductVariantShouldStillExistInTheProductCatalog(ProductVariantInterface $productVariant): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue(
                $this->client->index(Resources::PRODUCT_VARIANTS),
                'code',
                $productVariant->getCode(),
            ),
            'The product variant does not exist, but it should',
        );
    }

    /**
     * @Then I should be notified that this variant is in use and cannot be deleted
     */
    public function iShouldBeNotifiedThatThisVariantIsInUseAndCannotBeDeleted(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot delete, the product variant is in use.',
        );
    }

    /**
     * @Then inventory of this variant should not be tracked
     */
    public function inventoryOfThisVariantShouldNotBeTracked(): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'tracked', false));
    }

    /**
     * @Then inventory of this variant should be tracked
     */
    public function inventoryOfThisVariantShouldBeTracked(): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'tracked', true));
    }

    /**
     * @Then I should be notified that prices in all channels must be defined
     */
    public function iShouldBeNotifiedThatPricesInAllChannelsMustBeDefined(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'You must define price for every enabled channel.',
        );
    }

    /**
     * @Then I should be notified that price cannot be lower than 0
     */
    public function iShouldBeNotifiedThatPriceCannotBeLowerThanZero(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Price cannot be lower than 0.',
        );
    }

    /**
     * @Then I should be notified that price cannot be greater than max value allowed
     */
    public function iShouldBeNotifiedThatPriceCannotBeGreaterThanMaxValueAllowed(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Value must be less than %s.', self::HUGE_NUMBER),
        );
    }

    /**
     * @Then I should be notified that code is required
     */
    public function iShouldBeNotifiedThatCodeIsRequired(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Please enter the code.',
        );
    }

    /**
     * @Then I should be notified that current stock is required
     */
    public function iShouldBeNotifiedThatCurrentStockIsRequired(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The type of the "onHand" attribute must be "int", "NULL" given.',
        );
    }

    /**
     * @Then the :product product should have no variants
     */
    public function theProductShouldHaveNoVariants(ProductInterface $product): void
    {
        $this->iWantToViewAllVariantsOfThisProduct($product);
        $this->iShouldSeeNumberOfProductVariantsInTheList(0);
    }

    /**
     * @Then the :product product should have only one variant
     */
    public function theProductShouldHaveOnlyOneVariant(ProductInterface $product): void
    {
        $this->iWantToViewAllVariantsOfThisProduct($product);
        $this->iShouldSeeNumberOfProductVariantsInTheList(1);
    }

    /**
     * @Then /^(\d+) units of (this product) should be (on hand|on hold)$/
     */
    public function unitsOfThisProductShouldBeOn(
        int $quantity,
        ProductInterface $product,
        string $field,
    ): void {
        /** @var ProductVariantInterface $variant */
        $variant = $this->variantResolver->getVariant($product);
        Assert::isInstanceOf($variant, ProductVariantInterface::class);

        $this->iWantToViewAllVariantsOfThisProduct($product);
        $this->theVariantShouldHaveItemsOn($variant, $quantity, $field);
    }

    /**
     * @Then /^there should be no units of (this product) on hold$/
     */
    public function thereShouldBeNoUnitsOfThisProductOnHold(ProductInterface $product): void
    {
        $this->unitsOfThisProductShouldBeOn(0, $product, 'on hold');
    }

    /**
     * @Then /^the ("[^"]+" variant) should have (\d+) items (on hand|on hold)$/
     * @Then /^the (variant "[^"]+") should have (\d+) items (on hand|on hold)$/
     */
    public function theVariantShouldHaveItemsOn(ProductVariantInterface $variant, int $quantity, string $field): void
    {
        $variantsData = $this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'code',
            $variant->getCode(),
        );

        $variantData = array_pop($variantsData);

        Assert::same(
            (int) $variantData[StringInflector::nameToCamelCase($field)],
            $quantity,
        );
    }

    /**
     * @Then /^the ("[^"]+" variant of product "[^"]+") should have (\d+) items (on hand|on hold)$/
     * @Then /^the ("[^"]+" variant of "[^"]+" product) should have (\d+) items (on hand|on hold)$/
     * @Then /^(this variant) should have a (\d+) item currently in stock$/
     */
    public function theVariantOfProductShouldHaveItemsOn(
        ProductVariantInterface $variant,
        int $quantity,
        string $field = 'on hand',
    ): void {
        $actualQuantity = $this->responseChecker->getValue(
            $this->client->show(Resources::PRODUCT_VARIANTS, $variant->getCode()),
            StringInflector::nameToCamelCase($field),
        );

        Assert::same(
            (int) $actualQuantity,
            $quantity,
        );
    }

    /**
     * @Then I should be notified that code has to be unique
     */
    public function iShouldBeNotifiedThatCodeHasToBeUnique(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Product variant code must be unique.',
        );
    }

    /**
     * @Then I should be notified that this variant already exists
     */
    public function iShouldBeNotifiedThatThisVariantAlreadyExists(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Variant with this option set already exists.',
        );
    }

    /**
     * @Then I should be notified that height, width, depth and weight cannot be lower than 0
     */
    public function iShouldBeNotifiedThatIsHeightWidthDepthAndWeightCannotBeLowerThanZero(): void
    {
        $errors = $this->responseChecker->getError($this->client->getLastResponse());

        Assert::contains($errors, 'Height cannot be negative.');
        Assert::contains($errors, 'Width cannot be negative.');
        Assert::contains($errors, 'Depth cannot be negative.');
        Assert::contains($errors, 'Weight cannot be negative.');
    }

    /**
     * @Then the variant :productVariantName should have :optionName option as :optionValue
     */
    public function theVariantShouldHaveOptionAs(
        string $productVariantName,
        string $optionName,
        ProductOptionValueInterface $optionValue,
    ): void {
        Assert::true($this->responseChecker->hasValueInCollection(
            $this->client->getLastResponse(),
            'optionValues',
            $this->sectionAwareIriConverter->getIriFromResourceInSection($optionValue, 'admin'),
        ));
    }

    /**
     * @Then I should be notified that the variant can have only one value configured for a single option
     */
    public function iShouldBeNotifiedThatTheVariantCanHaveOnlyOneValueConfiguredForASingleOption(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The product variant can have only one value configured for a single option.',
        );
    }

    /**
     * @Then I should be notified that required options have not been configured
     */
    public function iShouldBeNotifiedThatRequiredOptionsHaveNotBeenConfigured(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The product variant must have configured values for all options chosen on the product.',
        );
    }

    /**
     * @Then I should be notified that on hand quantity must be greater than the number of on hold units
     */
    public function iShouldBeNotifiedThatOnHandQuantityMustBeGreaterThanTheNumberOfOnHoldUnits(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'On hand must be greater than the number of on hold units',
        );
    }
}
