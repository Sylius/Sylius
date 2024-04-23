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
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ManagingCatalogPromotionsContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I create a new catalog promotion with :code code and :name name
     */
    public function iCreateANewCatalogPromotionWithCodeAndName(string $code, string $name): void
    {
        $this->client->buildCreateRequest(Resources::CATALOG_PROMOTIONS);
        $this->client->addRequestData('code', $code);
        $this->client->addRequestData('name', $name);
        $this->client->create();
    }

    /**
     * @When I create a new catalog promotion with :code code and :name name and :priority priority
     */
    public function iCreateANewCatalogPromotionWithCodeAndNameAndPriority(string $code, string $name, int $priority): void
    {
        $this->client->buildCreateRequest(Resources::CATALOG_PROMOTIONS);
        $this->client->addRequestData('code', $code);
        $this->client->addRequestData('name', $name);
        $this->client->addRequestData('priority', $priority);
        $this->client->create();
    }

    /**
     * @When I create a new catalog promotion without specifying its code and name
     */
    public function iCreateANewCatalogPromotionWithoutSpecifyingItsCodeAndName(): void
    {
        $this->client->buildCreateRequest(Resources::CATALOG_PROMOTIONS);
        $this->client->create();
    }

    /**
     * @When I want to create a new catalog promotion
     */
    public function iWantToCreateNewCatalogPromotion(): void
    {
        $this->client->buildCreateRequest(Resources::CATALOG_PROMOTIONS);
    }

    /**
     * @When I specify its :field as :value
     * @When I :field it :value
     */
    public function iSpecifyItsAs(string $field, string $value): void
    {
        $this->client->addRequestData($field, $value);
    }

    /**
     * @When I set its priority to :priority
     */
    public function iSetsItsPriorityTo(int $priority): void
    {
        $this->client->addRequestData('priority', $priority);
    }

    /**
     * @When I specify its :field as :value in :localeCode
     */
    public function iSpecifyItsAsIn(string $field, string $value, string $localeCode): void
    {
        $data['translations'][$localeCode][$field] = $value;

        $this->client->updateRequestData($data);
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
     * @When I describe it as :description in :localeCode
     */
    public function iDescribeItAsIn(string $description, string $localeCode): void
    {
        $data = ['translations' => [$localeCode => []]];
        $data['translations'][$localeCode]['description'] = $description;

        $this->client->updateRequestData($data);
    }

    /**
     * @When I make it available in channel :channel
     */
    public function iMakeItAvailableInChannel(ChannelInterface $channel): void
    {
        $this->client->addRequestData('channels', [$this->sectionAwareIriConverter->getIriFromResourceInSection($channel, 'admin')]);
    }

    /**
     * @When /^I make (it) unavailable in (channel "[^"]+")$/
     */
    public function iMakeItUnavailableInChannel(CatalogPromotionInterface $catalogPromotion, ChannelInterface $channel): void
    {
        $channels = $this->responseChecker->getValue($this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode()), 'channels');

        foreach (array_keys($channels, $this->sectionAwareIriConverter->getIriFromResourceInSection($channel, 'admin')) as $key) {
            unset($channels[$key]);
        }

        $this->client->addRequestData('channels', $channels);
    }

    /**
     * @When I (try to) add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I rename the :catalogPromotion catalog promotion to :name
     * @When I try to rename the :catalogPromotion catalog promotion to :name
     */
    public function iRenameTheCatalogPromotionTo(CatalogPromotionInterface $catalogPromotion, string $name): void
    {
        $this->client->buildUpdateRequest(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());
        $this->client->updateRequestData(['name' => $name]);
        $this->client->update();
    }

    /**
     * @When I want to modify a catalog promotion :catalogPromotion
     * @When I modify a catalog promotion :catalogPromotion
     */
    public function iWantToModifyACatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->client->buildUpdateRequest(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);
    }

    /**
     * @When /^I add action that gives ("[^"]+") percentage discount$/
     */
    public function iAddActionThatGivesPercentageDiscount(float $amount): void
    {
        $actions = [[
            'type' => PercentageDiscountPriceCalculator::TYPE,
            'configuration' => [
                'amount' => $amount,
            ],
        ]];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When /^I add action that gives ("[^"]+") of fixed discount in the ("[^"]+" channel)$/
     */
    public function iAddActionThatGivesFixedDiscount(int $amount, ChannelInterface $channel): void
    {
        $actions = [[
            'type' => FixedDiscountPriceCalculator::TYPE,
            'configuration' => [
                $channel->getCode() => [
                    'amount' => $amount,
                ],
            ],
        ]];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When /^I add another action that gives ("[^"]+") percentage discount$/
     */
    public function iAddAnotherActionThatGivesPercentageDiscount(float $amount): void
    {
        $actions = $this->client->getContent()['actions'];

        $additionalAction = [[
            'type' => PercentageDiscountPriceCalculator::TYPE,
            'configuration' => [
                'amount' => $amount,
            ],
        ]];

        $actions = array_merge_recursive($actions, $additionalAction);

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When /^I add another scope that applies on ("[^"]+" variant)$/
     */
    public function iAddAnotherScopeThatGivesPercentageDiscount(ProductVariantInterface $productVariant): void
    {
        $scopes = $this->client->getContent()['scopes'];

        $additionalScope = [[
            'type' => InForVariantsScopeVariantChecker::TYPE,
            'configuration' => [
                'variants' => [$productVariant->getCode()],
            ],
        ]];

        $scopes = array_merge_recursive($scopes, $additionalScope);

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When /^I edit its action so that it reduces price by ("[^"]+")$/
     */
    public function iEditItsActionSoThatItReducesPriceBy(float $amount): void
    {
        $content = $this->client->getContent();
        $content['actions'][0]['configuration']['amount'] = $amount;
        $this->client->updateRequestData($content);
    }

    /**
     * @When I remove its every action
     */
    public function iRemoveItsEveryAction(): void
    {
        $content = $this->client->getContent();
        $content['actions'] = [];
        $this->client->setRequestData($content);
    }

    /**
     * @When I add invalid percentage discount action with non number in amount
     */
    public function iAddInvalidPercentageDiscountActionWithNonNumberInAmount(): void
    {
        $actions = [[
            'type' => PercentageDiscountPriceCalculator::TYPE,
            'configuration' => [
                'amount' => 'text',
            ],
        ]];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When I make it start at :startDate and ends at :endDate
     */
    public function iMakeCatalogPromotionOperateBetweenDates(string $startDate, string $endDate): void
    {
        $this->client->updateRequestData(['startDate' => $startDate, 'endDate' => $endDate]);
    }

    /**
     * @When I make it start yesterday and ends tomorrow
     */
    public function iMakeCatalogPromotionOperateBetweenYesterdayAndTomorrow(): void
    {
        $this->client->updateRequestData([
            'startDate' => (new \DateTime('yesterday'))->format('Y-m-d H:i:s'),
            'endDate' => (new \DateTime('tomorrow'))->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @When I make it start at :startDate
     */
    public function iMakeCatalogPromotionOperateFrom(string $startDate): void
    {
        $this->client->updateRequestData(['startDate' => $startDate]);
    }

    /**
     * @When I browse catalog promotions
     */
    public function iBrowseCatalogPromotions(): void
    {
        $this->client->index(Resources::CATALOG_PROMOTIONS);
    }

    /**
     * @When /^I add scope that applies on ("[^"]+" variant) and ("[^"]+" variant)$/
     * @When /^I add scope that applies on variants ("[^"]+" variant) and ("[^"]+" variant)$/
     */
    public function iAddScopeThatAppliesOnVariants(ProductVariantInterface $firstVariant, ProductVariantInterface $secondVariant): void
    {
        $scopes = [[
            'type' => InForVariantsScopeVariantChecker::TYPE,
            'configuration' => [
                'variants' => [
                    $firstVariant->getCode(),
                    $secondVariant->getCode(),
                ],
            ],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When I add catalog promotion scope for taxon without taxons
     */
    public function iAddCatalogPromotionScopeForTaxonWithoutTaxons(): void
    {
        $scopes = [[
            'type' => InForTaxonsScopeVariantChecker::TYPE,
            'configuration' => ['taxons' => []],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When I add catalog promotion scope for taxon with nonexistent taxons
     */
    public function iAddCatalogPromotionScopeForTaxonWithNonexistentTaxons(): void
    {
        $scopes = [[
            'type' => InForTaxonsScopeVariantChecker::TYPE,
            'configuration' => [
                'taxons' => [
                    'BAD_TAXON',
                    'EVEN_WORSE_TAXON',
                ],
            ],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When I add catalog promotion scope for product without products
     */
    public function iAddCatalogPromotionScopeForProductWithoutProducts(): void
    {
        $scopes = [[
            'type' => InForProductScopeVariantChecker::TYPE,
            'configuration' => ['products' => []],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When I add catalog promotion scope for product with nonexistent products
     */
    public function iAddCatalogPromotionScopeForProductsWithNonexistentProducts(): void
    {
        $scopes = [[
            'type' => InForProductScopeVariantChecker::TYPE,
            'configuration' => [
                'products' => [
                    'BAD_PRODUCT',
                    'EVEN_WORSE_PRODUCT',
                ],
            ],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When I add scope that applies on :taxon taxon
     */
    public function iAddScopeThatAppliesOnTaxon(TaxonInterface $taxon): void
    {
        $scopes = [[
            'type' => InForTaxonsScopeVariantChecker::TYPE,
            'configuration' => [
                'taxons' => [$taxon->getCode()],
            ],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When I add scope that applies on :product product
     */
    public function iAddScopeThatAppliesOnProduct(ProductInterface $product): void
    {
        $scopes = [[
            'type' => InForProductScopeVariantChecker::TYPE,
            'configuration' => [
                'products' => [$product->getCode()],
            ],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When /^I create an exclusive "([^"]+)" catalog promotion with ([^"]+) priority that applies on ("[^"]+" product) and reduces price by ("[^"]+") in ("[^"]+" channel)$/
     */
    public function iCreateAnExclusiveCatalogPromotionWithCodeAndNameAndPriorityThatAppliesOnProductAndReducesPriceByInChannel(
        string $name,
        int $priority,
        ProductInterface $product,
        float $discount,
        ChannelInterface $channel,
    ): void {
        $this->createCatalogPromotion($name, $priority, true, $product, $discount, $channel);
    }

    /**
     * @When /^I create a "([^"]+)" catalog promotion with ([^"]+) priority that applies on ("[^"]+" product) and reduces price by ("[^"]+") in ("[^"]+" channel)$/
     */
    public function iCreateACatalogPromotionWithCodeAndNameAndPriorityThatAppliesOnProductAndReducesPriceByInChannel(
        string $name,
        int $priority,
        ProductInterface $product,
        float $discount,
        ChannelInterface $channel,
    ): void {
        $this->createCatalogPromotion($name, $priority, false, $product, $discount, $channel);
    }

    /**
     * @When I remove its every scope
     */
    public function iRemoveItsEveryScope(): void
    {
        $content = $this->client->getContent();
        $content['scopes'] = [];
        $this->client->setRequestData($content);
    }

    /**
     * @When /^I edit ("[^"]+" catalog promotion) to be applied on ("[^"]+" variant)$/
     */
    public function iEditCatalogPromotionToBeAppliedOnVariant(
        CatalogPromotionInterface $catalogPromotion,
        ProductVariantInterface $productVariant,
    ): void {
        $this->changeFirstScopeConfigurationTo(
            $catalogPromotion,
            InForVariantsScopeVariantChecker::TYPE,
            ['variants' => [$productVariant->getCode()]],
        );
    }

    /**
     * @When /^I edit ("[^"]+" catalog promotion) to be applied on ("[^"]+" taxon)$/
     */
    public function iEditCatalogPromotionToBeAppliedOnTaxon(
        CatalogPromotionInterface $catalogPromotion,
        TaxonInterface $taxon,
    ): void {
        $this->changeFirstScopeConfigurationTo(
            $catalogPromotion,
            InForTaxonsScopeVariantChecker::TYPE,
            ['taxons' => [$taxon->getCode()]],
        );
    }

    /**
     * @When /^I edit ("[^"]+" catalog promotion) to be applied on ("[^"]+" product)$/
     */
    public function iEditCatalogPromotionToBeAppliedOnProduct(
        CatalogPromotionInterface $catalogPromotion,
        ProductInterface $product,
    ): void {
        $this->changeFirstScopeConfigurationTo(
            $catalogPromotion,
            InForProductScopeVariantChecker::TYPE,
            ['products' => [$product->getCode()]],
        );
    }

    /**
     * @When I disable :catalogPromotion catalog promotion
     */
    public function iDisableThisCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->toggleCatalogPromotion($catalogPromotion, false);
    }

    /**
     * @When I enable :catalogPromotion catalog promotion
     */
    public function iEnableCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->toggleCatalogPromotion($catalogPromotion, true);
    }

    /**
     * @When /^I edit ("[^"]+" catalog promotion) to have ("[^"]+") discount$/
     */
    public function iEditCatalogPromotionToHaveDiscount(CatalogPromotionInterface $catalogPromotion, float $amount): void
    {
        $this->client->buildUpdateRequest(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());
        $scopes = [[
            'type' => PercentageDiscountPriceCalculator::TYPE,
            'configuration' => [
                'amount' => $amount,
            ],
        ]];

        $this->client->updateRequestData(['actions' => $scopes]);
        $this->client->update();
    }

    /**
     * @When /^I edit ("[^"]+" catalog promotion) to have ("[^"]+") of fixed discount in the ("[^"]+" channel)$/
     */
    public function iEditCatalogPromotionToHaveFixedDiscountInTheChannel(
        CatalogPromotionInterface $catalogPromotion,
        int $amount,
        ChannelInterface $channel,
    ): void {
        $this->client->buildUpdateRequest(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());
        $content = $this->client->getContent();

        $content['actions'] = [[
            'type' => FixedDiscountPriceCalculator::TYPE,
            'configuration' => [$channel->getCode() => ['amount' => $amount]],
        ]];

        $this->client->setRequestData($content);
        $this->client->update();
    }

    /**
     * @When /^I edit it to have ("[^"]+") of fixed discount in the ("[^"]+" channel)$/
     */
    public function iEditItToHaveFixedDiscountInTheChannel(
        int $amount,
        ChannelInterface $channel,
    ): void {
        $content = $this->client->getContent();

        $content['actions'] = [[
            'type' => FixedDiscountPriceCalculator::TYPE,
            'configuration' => [$channel->getCode() => ['amount' => $amount]],
        ]];

        $this->client->setRequestData($content);
    }

    /**
     * @When I edit it to have empty amount of percentage discount
     */
    public function iEditItToHaveEmptyPercentageDiscount(): void
    {
        $content = $this->client->getContent();

        $content['actions'] = [[
            'type' => PercentageDiscountPriceCalculator::TYPE,
            'configuration' => ['amount' => null],
        ]];

        $this->client->setRequestData($content);
    }

    /**
     * @When I edit it to have empty amount of fixed discount in the :channel channel
     */
    public function iEditItToHaveEmptyFixedDiscountInTheChannel(ChannelInterface $channel): void
    {
        $content = $this->client->getContent();

        $content['actions'] = [[
            'type' => FixedDiscountPriceCalculator::TYPE,
            'configuration' => [$channel->getCode() => ['amount' => null]],
        ]];

        $this->client->setRequestData($content);
    }

    /**
     * @When I add catalog promotion scope with nonexistent type
     */
    public function iAddCatalogPromotionScopeWithNonexistentType(): void
    {
        $scopes = [[
            'type' => 'nonexistent_scope',
            'configuration' => [
                'config' => 'config',
            ],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When I add for variants scope with the wrong configuration
     */
    public function iAddForVariantsScopeWithTheWrongConfiguration(): void
    {
        $scopes = [[
            'type' => InForVariantsScopeVariantChecker::TYPE,
            'configuration' => [
                'variants' => ['wrong_code'],
            ],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When I add for variants scope without variants configured
     */
    public function iAddForVariantsScopeWithoutVariantsConfigured(): void
    {
        $scopes = [[
            'type' => InForVariantsScopeVariantChecker::TYPE,
            'configuration' => [
                'variants' => [],
            ],
        ]];

        $this->client->addRequestData('scopes', $scopes);
    }

    /**
     * @When I add percentage discount action without amount configured
     */
    public function iAddPercentageDiscountActionWithoutAmountConfigured(): void
    {
        $actions = [[
            'type' => PercentageDiscountPriceCalculator::TYPE,
            'configuration' => [
                'amount' => null,
            ],
        ]];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When I add fixed discount action without amount configured for the :channel channel
     */
    public function iAddFixedDiscountActionWithoutAmountConfigured(ChannelInterface $channel): void
    {
        $actions = [[
            'type' => FixedDiscountPriceCalculator::TYPE,
            'configuration' => [$channel->getCode() => ['amount' => null]],
        ]];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When I add invalid fixed discount action with non number in amount for the :channel channel
     */
    public function iAddInvalidFixedDiscountActionWithNonNumberInAmountForTheChannel(
        ChannelInterface $channel,
    ): void {
        $actions = [[
            'type' => FixedDiscountPriceCalculator::TYPE,
            'configuration' => [$channel->getCode() => ['amount' => 'wrong value']],
        ]];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When I add invalid fixed discount action configured for nonexistent channel
     */
    public function iAddInvalidFixedDiscountActionConfiguredForNonexistentChannel(): void
    {
        $actions = [[
            'type' => FixedDiscountPriceCalculator::TYPE,
            'configuration' => ['nonexistent_channel' => ['amount' => 1000]],
        ]];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When I add catalog promotion action with nonexistent type
     */
    public function iAddCatalogPromotionActionWithNonexistentType(): void
    {
        $actions = [[
            'type' => 'nonexistent_action',
            'configuration' => [],
        ]];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When /^I make (this catalog promotion) unavailable in the ("[^"]+" channel)$/
     * @When /^I make the ("[^"]+" catalog promotion) unavailable in the ("[^"]+" channel)$/
     */
    public function iMakeThisCatalogPromotionUnavailableInTheChannel(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel,
    ): void {
        $catalogPromotionCode = $catalogPromotion->getCode();
        Assert::notNull($catalogPromotionCode);

        $this->client->buildUpdateRequest(Resources::CATALOG_PROMOTIONS, $catalogPromotionCode);
        $content = $this->client->getContent();
        foreach (array_keys($content['channels'], $this->iriConverter->getIriFromResource($channel)) as $key) {
            unset($content['channels'][$key]);
        }

        $this->client->setRequestData($content);
        $this->client->update();
    }

    /**
     * @When /^I make (this catalog promotion) available in the ("[^"]+" channel)$/
     * @When /^I make ("[^"]+" catalog promotion) available in the ("[^"]+" channel)$/
     */
    public function iMakeThisCatalogPromotionAvailableInTheChannel(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel,
    ): void {
        $this->client->buildUpdateRequest(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());
        $content = $this->client->getContent();
        $content['channels'][] = $this->iriConverter->getIriFromResource($channel);
        $this->client->updateRequestData(['channels' => $content['channels']]);
        $this->client->update();
    }

    /**
     * @When /^I switch (this catalog promotion) availability from the ("[^"]+" channel) to the ("[^"]+" channel)$/
     * @When /^I switch ("[^"]+" catalog promotion) availability from the ("[^"]+" channel) to the ("[^"]+" channel)$/
     */
    public function iSwitchThisCatalogPromotionAvailabilityFromTheChannelToTheChannel(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $removedChannel,
        ChannelInterface $addedChannel,
    ): void {
        $catalogPromotionCode = $catalogPromotion->getCode();
        Assert::notNull($catalogPromotionCode);

        $this->client->buildUpdateRequest(Resources::CATALOG_PROMOTIONS, $catalogPromotionCode);
        $content = $this->client->getContent();
        foreach (array_keys($content['channels'], $this->iriConverter->getIriFromResource($removedChannel)) as $key) {
            unset($content['channels'][$key]);
        }

        $content['channels'][] = $this->iriConverter->getIriFromResource($addedChannel);
        $this->client->setRequestData($content);
        $this->client->update();
    }

    /**
     * @When I view details of the catalog promotion :catalogPromotion
     */
    public function iViewDetailsOfTheCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);
    }

    /**
     * @When I( try to) change its end date to :endDate
     */
    public function iChangeItsEndDateTo(string $endDate): void
    {
        $this->client->updateRequestData(['endDate' => $endDate]);
    }

    /**
     * @When /^I search by "([^"]+)" (code|name)$/
     */
    public function iSearchByName(string $phrase, string $field): void
    {
        $this->client->addFilter($field, $phrase);
        $this->client->filter();
    }

    /**
     * @When I filter by :channel channel
     */
    public function iFilterByChannel(ChannelInterface $channel): void
    {
        $this->client->addFilter('channel', $this->iriConverter->getIriFromResource($channel));
        $this->client->filter();
    }

    /**
     * @When I filter enabled catalog promotions
     */
    public function iFilterEnabledCatalogPromotions(): void
    {
        $this->client->addFilter('enabled', true);
        $this->client->filter();
    }

    /**
     * @When /^I filter by (active|failed|inactive|processing) state$/
     */
    public function iFilterByState(string $state): void
    {
        $this->client->addFilter('state', $state);
        $this->client->filter();
    }

    /**
     * @When /^I filter by (end|start) date up to "(\d{4}-\d{2}-\d{2})"$/
     */
    public function iFilterDateUpTo(string $dateType, string $date): void
    {
        $this->client->addFilter(sprintf('%sDate[before]', $dateType), $date);
        $this->client->filter();
    }

    /**
     * @When /^I filter by (end|start) date from "(\d{4}-\d{2}-\d{2})"$/
     */
    public function iFilterByDateFrom(string $dateType, string $date): void
    {
        $this->client->addFilter(sprintf('%sDate[after]', $dateType), $date);
        $this->client->filter();
    }

    /**
     * @When /^I filter by (end|start) date from "(\d{4}-\d{2}-\d{2})" up to "(\d{4}-\d{2}-\d{2})"$/
     */
    public function iFilterByDateFromDateToDate(string $dateType, string $fromDate, string $toDate): void
    {
        $this->client->addFilter(sprintf('%sDate[after]', $dateType), $fromDate);
        $this->client->addFilter(sprintf('%sDate[before]', $dateType), $toDate);
        $this->client->filter();
    }

    /**
     * @When I sort catalog promotions by :order :field
     */
    public function iSortCatalogPromotionByOrderField(string $order, string $field): void
    {
        $this->client->addFilter(
            sprintf('order[%s]', lcfirst(str_replace(' ', '', ucwords($field)))),
            $order === 'descending' ? 'desc' : 'asc',
        );
        $this->client->filter();
    }

    /**
     * @When I request the removal of :catalogPromotion catalog promotion
     */
    public function iRequestTheRemovalOfCatalogPromotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->client->delete(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());
    }

    /**
     * @Then I should be notified that the removal operation has started successfully
     */
    public function iShouldBeNotifiedThatTheRemovalOperationHasStartedSuccessfully(): void
    {
        Assert::true(
            $this->responseChecker->isAccepted($this->client->getLastResponse()),
            'Removal operation has not started successfully',
        );
    }

    /**
     * @Then there should be :amount new catalog promotion on the list
     * @Then there should be :amount catalog promotions on the list
     * @Then there should be an empty list of catalog promotions
     */
    public function thereShouldBeNewCatalogPromotionOnTheList(int $amount = 0): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->index(Resources::CATALOG_PROMOTIONS)), $amount);
    }

    /**
     * @Then /^it should have ("[^"]+") discount$/
     */
    public function itShouldHaveDiscount(float $amount): void
    {
        $catalogPromotion = $this->responseChecker->getCollection($this->client->getLastResponse())[0];

        Assert::same($catalogPromotion['actions'][0]['configuration']['amount'], $amount);
    }

    /**
     * @Then /^the ("[^"]+" catalog promotion) should have ("[^"]+") of fixed discount in the ("[^"]+" channel)$/
     */
    public function theCatalogPromotionShouldHaveFixedDiscountInTheChannel(
        CatalogPromotionInterface $catalogPromotion,
        int $amount,
        ChannelInterface $channel,
    ): void {
        $catalogPromotion = $this->responseChecker->getCollection($this->client->getLastResponse())[0];

        Assert::same($catalogPromotion['actions'][0]['configuration'][$channel->getCode()]['amount'], $amount);
    }

    /**
     * @Then /^this catalog promotion should have ("[^"]+") of fixed discount in the ("[^"]+" channel)$/
     * @Then /^it should reduce price by ("[^"]+") in the ("[^"]+" channel)$/
     */
    public function thisCatalogPromotionShouldHaveFixedDiscountInTheChannel(int $amount, ChannelInterface $channel): void
    {
        $catalogPromotionActions = $this->responseChecker->getValue($this->client->getLastResponse(), 'actions');

        foreach ($catalogPromotionActions as $catalogPromotionAction) {
            if (
                $catalogPromotionAction['type'] === FixedDiscountPriceCalculator::TYPE &&
                $catalogPromotionAction['configuration'][$channel->getCode()]['amount'] === $amount
            ) {
                return;
            }
        }

        throw new \Exception(sprintf(
            'There is no "%s" action with %d for "%s" channel',
            FixedDiscountPriceCalculator::TYPE,
            $amount,
            $channel->getName(),
        ));
    }

    /**
     * @Then /^this catalog promotion should have ("[^"]+") percentage discount$/
     * @Then /^it should reduce price by ("[^"]+")$/
     */
    public function thisCatalogPromotionShouldHavePercentageDiscount(float $amount): void
    {
        $catalogPromotionActions = $this->responseChecker->getResponseContent($this->client->getLastResponse())['actions'];
        foreach ($catalogPromotionActions as $catalogPromotionAction) {
            if (
                $catalogPromotionAction['configuration']['amount'] === $amount &&
                $catalogPromotionAction['type'] === PercentageDiscountPriceCalculator::TYPE
            ) {
                return;
            }
        }

        throw new \Exception(sprintf('There is no "%s" action with %f', PercentageDiscountPriceCalculator::TYPE, $amount));
    }

    /**
     * @Then it should have :code code and :name name
     */
    public function itShouldHaveCodeAndName(string $code, string $name): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->client->index(Resources::CATALOG_PROMOTIONS), ['code' => $code, 'name' => $name]),
            sprintf('Cannot find catalog promotions with code "%s" and name "%s" in the list', $code, $name),
        );
    }

    /**
     * @Then it should have priority equal to :priority
     */
    public function itShouldHavePriorityEqualTo(int $priority): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues($this->client->index(Resources::CATALOG_PROMOTIONS), ['priority' => $priority]),
            sprintf('Cannot find catalog promotions with priority "%d"', $priority),
        );
    }

    /**
     * @Then the catalog promotions named :firstName and :secondName should be in the registry
     * @Then the catalog promotion named :firstName should be in the registry
     */
    public function theCatalogPromotionsNamedShouldBeInTheRegistry(string ...$names): void
    {
        foreach ($names as $name) {
            Assert::true(
                $this->responseChecker->hasItemWithValue($this->client->index(Resources::CATALOG_PROMOTIONS), 'name', $name),
                sprintf('Cannot find catalog promotions with name "%s" in the list', $name),
            );
        }
    }

    /**
     * @Then the catalog promotion named :catalogPromotion should operate between :startDate and :endDate
     * @Then /^(it) should operate between "([^"]+)" and "([^"]+)"$/
     * @Then /^(it) should start at "([^"]+)" and end at "([^"]+)"$/
     * @Then /^(this catalog promotion) should operate between "([^"]+)" and "([^"]+)"$/
     */
    public function theCatalogPromotionNamedShouldOperateBetweenDates(
        CatalogPromotionInterface $catalogPromotion,
        string $startDate,
        string $endDate,
    ): void {
        $response = $this->client->index(Resources::CATALOG_PROMOTIONS);

        Assert::true(
            $this->responseChecker->hasItemWithValues(
                $response,
                ['name' => $catalogPromotion->getName(), 'startDate' => $startDate . ':00', 'endDate' => $endDate . ':00'],
            ),
            sprintf(
                'Cannot find catalog promotions with name "%s" operating between "%s" and "%s" in the list',
                $catalogPromotion->getName(),
                $startDate,
                $endDate,
            ),
        );
    }

    /**
     * @Then the catalog promotion named :catalogPromotion should have priority :priority
     */
    public function theCatalogPromotionNamedShouldHavePriority(
        CatalogPromotionInterface $catalogPromotion,
        int $priority,
    ): void {
        $response = $this->client->index(Resources::CATALOG_PROMOTIONS);

        Assert::true(
            $this->responseChecker->hasItemWithValues(
                $response,
                ['name' => $catalogPromotion->getName(), 'priority' => $priority],
            ),
            sprintf(
                'Cannot find catalog promotions with name "%s" and priority "%s" in the list',
                $catalogPromotion->getName(),
                $priority,
            ),
        );
    }

    /**
     * @Then /^(it) should operate between yesterday and tomorrow$/
     */
    public function theCatalogPromotionsNamedShouldOperateBetweenYesterdayAndTomorrow(
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $response = $this->client->index(Resources::CATALOG_PROMOTIONS);

        Assert::true(
            $this->responseChecker->hasItemWithValues(
                $response,
                [
                    'name' => $catalogPromotion->getName(),
                    'startDate' => (new \DateTime('yesterday'))->format('Y-m-d H:i:s'),
                    'endDate' => (new \DateTime('tomorrow'))->format('Y-m-d H:i:s'),
                ],
            ),
            sprintf(
                'Cannot find catalog promotions with name "%s" operating between "%s" and "%s" in the list',
                $catalogPromotion->getName(),
                (new \DateTime('yesterday'))->format('Y-m-d H:i:s'),
                (new \DateTime('tomorrow'))->format('Y-m-d H:i:s'),
            ),
        );
    }

    /**
     * @Then /^(it) should be (inactive|active)$/
     */
    public function itShouldBe(CatalogPromotionInterface $catalogPromotion, string $state): void
    {
        Assert::true($this->responseChecker->hasItemWithValues(
            $this->client->getLastResponse(),
            ['name' => $catalogPromotion->getName(), 'state' => $state],
        ));
    }

    /**
     * @Then /^(its) priority should be ([^"]+)$/
     */
    public function itPriorityShouldBe(CatalogPromotionInterface $catalogPromotion, int $priority): void
    {
        Assert::true($this->responseChecker->hasItemWithValues(
            $this->client->getLastResponse(),
            ['priority' => $priority],
        ));
    }

    /**
     * @Then /^(this catalog promotion) should(?:| still) be (inactive|active)$/
     */
    public function thisCatalogPromotionShouldBe(CatalogPromotionInterface $catalogPromotion, string $state): void
    {
        $response = $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        Assert::true($this->responseChecker->hasValue($response, 'state', $state));
    }

    /**
     * @Then /^("[^"]+" catalog promotion) should apply to ("[^"]+" variant) and ("[^"]+" variant)$/
     */
    public function catalogPromotionShouldApplyToVariants(
        CatalogPromotionInterface $catalogPromotion,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
    ): void {
        Assert::same(
            ['variants' => [$firstVariant->getCode(), $secondVariant->getCode()]],
            $this->responseChecker->getCollection($this->client->getLastResponse())[0]['scopes'][0]['configuration'],
        );

        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);
    }

    /**
     * @Then :catalogPromotionName catalog promotion should apply to all products from :taxon taxon
     */
    public function catalogPromotionShouldApplyToAllProductsFromTaxons(string $catalogPromotionName, TaxonInterface $taxon): void
    {
        Assert::same(
            ['taxons' => [$taxon->getCode()]],
            $this->responseChecker->getCollection($this->client->getLastResponse())[0]['scopes'][0]['configuration'],
        );
    }

    /**
     * @Then the :catalogPromotionName catalog promotion should apply to all variants of :product product
     */
    public function theCatalogPromotionShouldApplyToAllVariantsOfProduct(string $catalogPromotionName, ProductInterface $product): void
    {
        Assert::same(
            ['products' => [$product->getCode()]],
            $this->responseChecker->getCollection($this->client->getLastResponse())[0]['scopes'][0]['configuration'],
        );
    }

    /**
     * @Then the catalog promotion :catalogPromotion should be available in channel :channel
     * @Then /^(this catalog promotion) should be available in (channel "[^"]+")$/
     */
    public function itShouldBeAvailableInChannel(CatalogPromotionInterface $catalogPromotion, ChannelInterface $channel): void
    {
        Assert::true(
            $this->responseChecker->hasValueInCollection(
                $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode()),
                'channels',
                $this->sectionAwareIriConverter->getIriFromResourceInSection($channel, 'admin'),
            ),
            sprintf('Catalog promotion is not assigned to %s channel', $channel->getName()),
        );

        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);
    }

    /**
     * @Then /^(this catalog promotion) should not be available in (channel "[^"]+")$/
     */
    public function itShouldNotBeAvailableInChannel(
        CatalogPromotionInterface $catalogPromotion,
        ChannelInterface $channel,
    ): void {
        Assert::false(
            $this->responseChecker->hasValueInCollection(
                $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode()),
                'channels',
                $this->sectionAwareIriConverter->getIriFromResourceInSection($channel, 'admin'),
            ),
            sprintf('Catalog promotion is assigned to %s channel', $channel->getName()),
        );
    }

    /**
     * @Then I should be notified that catalog promotion has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Catalog promotion could not be created',
        );
    }

    /**
     * @Then I should be notified that not all channels are filled
     */
    public function iShouldBeNotifiedThatNotAllChannelsAreFilled(): void
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        Assert::same($response['violations'][0]['message'], 'This field is missing.');
    }

    /**
     * @Then /^(this catalog promotion) name should(?:| still) be "([^"]+)"$/
     */
    public function thisCatalogPromotionNameShouldBe(CatalogPromotionInterface $catalogPromotion, string $name): void
    {
        $response = $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        Assert::true(
            $this->responseChecker->hasValue($response, 'name', $name),
            sprintf('Catalog promotion\'s name %s does not exist', $name),
        );
    }

    /**
     * @Then /^(this catalog promotion) should be (labelled|described) as "([^"]+)" in ("[^"]+" locale)$/
     */
    public function thisCatalogPromotionLabelInLocaleShouldBe(
        CatalogPromotionInterface $catalogPromotion,
        string $field,
        string $value,
        string $localeCode,
    ): void {
        $fieldsMapping = [
            'labelled' => 'label',
            'described' => 'description',
        ];

        $response = $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        Assert::true($this->responseChecker->hasTranslation($response, $localeCode, $fieldsMapping[$field], $value));
    }

    /**
     * @Then /^(this catalog promotion) should be applied on ("[^"]+" variant)$/
     */
    public function thisCatalogPromotionShouldBeAppliedOnVariant(
        CatalogPromotionInterface $catalogPromotion,
        ProductVariantInterface $productVariant,
    ): void {
        $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        Assert::true($this->catalogPromotionHasValuesInScopeConfiguration('variants', $productVariant->getCode()));
    }

    /**
     * @Then it should apply on :variant variant
     */
    public function itShouldApplyOnVariant(ProductVariantInterface $variant): void
    {
        Assert::true($this->catalogPromotionHasValuesInScopeConfiguration('variants', $variant->getCode()));
    }

    /**
     * @Then it should apply on :product product
     */
    public function itShouldApplyOnProduct(ProductInterface $product): void
    {
        Assert::true($this->catalogPromotionHasValuesInScopeConfiguration('products', $product->getCode()));
    }

    /**
     * @Then /^(this catalog promotion) should be applied on ("[^"]+" taxon)$/
     */
    public function thisCatalogPromotionShouldBeAppliedOnTaxon(
        CatalogPromotionInterface $catalogPromotion,
        TaxonInterface $taxon,
    ): void {
        $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        Assert::true($this->catalogPromotionHasValuesInScopeConfiguration('taxons', $taxon->getCode()));
    }

    /**
     * @Then /^(this catalog promotion) should not be applied on ("[^"]+" variant)$/
     */
    public function thisCatalogPromotionShouldNotBeAppliedOn(
        CatalogPromotionInterface $catalogPromotion,
        ProductVariantInterface $productVariant,
    ): void {
        $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        Assert::false($this->catalogPromotionHasValuesInScopeConfiguration('variants', $productVariant->getCode()));
    }

    /**
     * @Then /^(this catalog promotion) should be applied on ("[^"]+" product)$/
     */
    public function thisCatalogPromotionShouldBeAppliedOnProduct(
        CatalogPromotionInterface $catalogPromotion,
        ProductInterface $product,
    ): void {
        $this->client->show(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        Assert::true($this->catalogPromotionHasValuesInScopeConfiguration('products', $product->getCode()));
    }

    /**
     * @Then I should be notified that code and name are required
     */
    public function iShouldBeNotifiedThatCodeAndNameAreRequired(): void
    {
        $validationError = $this->responseChecker->getError($this->client->getLastResponse());

        Assert::contains($validationError, 'code: Please enter catalog promotion code.');
        Assert::contains($validationError, 'name: Please enter catalog promotion name.');
    }

    /**
     * @Then I should be notified that catalog promotion with this code already exists
     */
    public function iShouldBeNotifiedThatCatalogPromotionWithThisCodeAlreadyExists(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Catalog promotion has been created successfully, but it should not',
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'code: The catalog promotion with given code already exists.',
        );
    }

    /**
     * @Then there should still be only one catalog promotion with code :code
     */
    public function thereShouldStillBeOnlyOneCatalogPromotionWithCode(string $code): void
    {
        Assert::count($this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::CATALOG_PROMOTIONS), 'code', $code), 1);
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);
        $this->client->update();

        Assert::false(
            $this->responseChecker->hasValue($this->client->getLastResponse(), 'code', 'NEW_CODE'),
            'The code has been changed, but it should not',
        );
    }

    /**
     * @Then I should be notified that a discount amount is required
     */
    public function iShouldBeNotifiedThatADiscountAmountIsRequired(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Catalog promotion has been created successfully, but it should not',
        );
        Assert::contains(
            $this->responseChecker->getError($response),
            'The percentage discount amount must be configured.',
        );
    }

    /**
     * @Then /^I should be notified that type of action is invalid$/
     */
    public function iShouldBeNotifiedThatTypeOfActionIsInvalid(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Catalog promotion action type is invalid. Available types are fixed_discount, percentage_discount.',
        );
    }

    /**
     * @Then /^I should be notified that type of scope is invalid$/
     */
    public function iShouldBeNotifiedThatTypeOfScopeIsInvalid(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Catalog promotion scope type is invalid. Available types are for_products, for_taxons, for_variants.',
        );
    }

    /**
     * @Then I should be notified that a discount amount should be between 0% and 100%
     */
    public function iShouldBeNotifiedThatADiscountAmountShouldBeBetween0And100(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The percentage discount amount must be between 0% and 100%.',
        );
    }

    /**
     * @Then I should be notified that the percentage amount should be a number and cannot be empty
     */
    public function iShouldBeNotifiedThatDiscountAmountShouldBeANumberAndCannotBeEmpty(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The percentage discount amount must be a number and can not be empty.',
        );
    }

    /**
     * @Then I should be notified that the fixed amount should be a number and cannot be empty
     */
    public function iShouldBeNotifiedThatTheFixedAmountShouldBeANumber(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Provided configuration contains errors. Please add the fixed discount amount that is a number greater than 0.',
        );
    }

    /**
     * @Then I should be notified that at least one of the provided channel codes does not exist
     */
    public function iShouldBeNotifiedThatAtLeastOneOfTheProvidedChannelCodesDoesNotExist(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Provided configuration contains errors. At least one of the provided channel codes does not exist.',
        );
    }

    /**
     * @Then I should be notified that scope configuration is invalid
     */
    public function iShouldBeNotifiedThatScopeConfigurationIsInvalid(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Product variant with code wrong_code does not exist.',
        );
    }

    /**
     * @Then /^I should be notified that I must add at least one (product|taxon)$/
     */
    public function iShouldBeNotifiedThatIMustAddAtLeastOne(string $entity): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('Provided configuration contains errors. Please add at least 1 %s.', $entity),
        );
    }

    /**
     * @Then /^I should be notified that I can add only existing (product|taxon)$/
     */
    public function iShouldBeNotifiedThatICanAddOnlyExisting(string $entity): void
    {
        Assert::regex(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('/%s with code [^"]+ does not exist.$/', ucfirst($entity)),
        );
    }

    /**
     * @Then I should be notified that at least 1 variant is required
     */
    public function iShouldBeNotifiedThatAtLeast1VariantIsRequired(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Please add at least 1 variant.',
        );
    }

    /**
     * @Then I should not be able to edit it due to wrong state
     */
    public function iShouldNotBeAbleToEditItDueToWrongState(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The catalog promotion cannot be edited as it is currently being processed.',
        );
    }

    /**
     * @Then its name should be :name
     */
    public function itsNameShouldBe(string $name): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'name', $name));
    }

    /**
     * @Given it should be exclusive
     */
    public function itShouldBeExclusive(): void
    {
        Assert::true($this->responseChecker->hasValue($this->client->getLastResponse(), 'exclusive', true));
    }

    /**
     * @Given it should not be exclusive
     */
    public function itShouldNotBeExclusive(): void
    {
        Assert::false($this->responseChecker->hasValue($this->client->getLastResponse(), 'exclusive', true));
    }

    /**
     * @Then I should get information that the end date cannot be set before start date
     */
    public function iShouldGetInformationThatTheEndDateCannotBeSetBeforeStartDate(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'endDate: End date cannot be set before start date.',
        );
    }

    /**
     * @Then I should see a catalog promotion with name :name
     */
    public function iShouldSeeACatalogPromotionWithName(string $name): void
    {
        $response = $this->client->getLastResponse();

        Assert::true(
            $this->responseChecker->hasItemWithValue($response, 'name', $name),
            sprintf('No catalog promotion with name "%s" has been found.', $name),
        );
    }

    /**
     * @Then I should not see a catalog promotion with name :name
     */
    public function iShouldNotSeeACatalogPromotionWithName(string $name): void
    {
        $response = $this->client->getLastResponse();

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'name', $name),
            sprintf('Catalog promotion with name "%s" has been found, but should not.', $name),
        );
    }

    /**
     * @Then I should see :count catalog promotions on the list
     */
    public function iShouldSeeCountCatalogPromotionsOnTheList(int $count): void
    {
        Assert::count($this->responseChecker->getCollection($this->client->getLastResponse()), $count);
    }

    /**
     * @Then the first catalog promotion should have code :code
     */
    public function theFirstCatalogPromotionShouldHaveCode(string $code): void
    {
        $catalogPromotions = $this->responseChecker->getCollection($this->client->getLastResponse());

        Assert::same(reset($catalogPromotions)['code'], $code);
    }

    private function catalogPromotionHasValuesInScopeConfiguration(string $configurationKey, string ...$values): bool
    {
        $response = $this->responseChecker->getResponseContent($this->client->getLastResponse());
        $configuration = $response['scopes'] ?? [];
        if ([] === $configuration || empty($configuration[0])) {
            return false;
        }

        foreach ($configuration as $scope) {
            if (!isset($scope['configuration'][$configurationKey])) {
                continue;
            }

            foreach ($values as $value) {
                if (in_array($value, $scope['configuration'][$configurationKey], true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function toggleCatalogPromotion(CatalogPromotionInterface $catalogPromotion, bool $enabled): void
    {
        $this->client->buildUpdateRequest(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        $this->client->updateRequestData(['enabled' => $enabled]);
        $this->client->update();

        $this->sharedStorage->set('catalog_promotion', $catalogPromotion);
    }

    private function changeFirstScopeConfigurationTo(
        CatalogPromotionInterface $catalogPromotion,
        string $type,
        array $configuration,
    ): void {
        $this->client->buildUpdateRequest(Resources::CATALOG_PROMOTIONS, $catalogPromotion->getCode());

        $content = $this->client->getContent();
        unset($content['scopes'][0]);
        $content['scopes'][0]['type'] = $type;
        $content['scopes'][0]['configuration'] = $configuration;

        $this->client->setRequestData($content);

        $this->client->update();
    }

    private function createCatalogPromotion(
        string $name,
        int $priority,
        bool $exclusive,
        ProductInterface $product,
        float $discount,
        ChannelInterface $channel,
    ): void {
        $this->client->buildCreateRequest(Resources::CATALOG_PROMOTIONS);

        $this->client->updateRequestData([
            'code' => StringInflector::nameToCode($name),
            'name' => $name,
            'priority' => $priority,
            'enabled' => true,
            'channels' => [$this->iriConverter->getIriFromResource($channel)],
            'exclusive' => $exclusive,
            'translations' => ['en_US' => [
                'label' => $name,
            ]],
            'actions' => [[
                'type' => PercentageDiscountPriceCalculator::TYPE,
                'configuration' => [
                    'amount' => $discount,
                ],
            ]],
            'scopes' => [[
                'type' => InForProductScopeVariantChecker::TYPE,
                'configuration' => [
                    'products' => [$product->getCode()],
                ],
            ]],
        ]);

        $this->client->create();
    }
}
