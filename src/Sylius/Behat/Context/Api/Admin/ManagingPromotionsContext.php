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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\CustomerGroupRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

final class ManagingPromotionsContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private IriConverterInterface $iriConverter,
        private SectionAwareIriConverterInterface $sectionAwareIriConverter,
    ) {
    }

    /**
     * @When I want to browse promotions
     * @When I browse promotions
     */
    public function iWantToBrowsePromotions(): void
    {
        $this->client->index(Resources::PROMOTIONS);
    }

    /**
     * @When I want to create a new promotion
     */
    public function iWantToCreateANewPromotion(): void
    {
        $this->client->buildCreateRequest(Resources::PROMOTIONS);
    }

    /**
     * @When I want to modify a :promotion promotion
     * @When /^I want to modify (this promotion)$/
     * @When I modify a :promotion promotion
     */
    public function iWantToModifyAPromotion(PromotionInterface $promotion): void
    {
        $this->client->buildUpdateRequest(Resources::PROMOTIONS, $promotion->getCode());
    }

    /**
     * @When I archive the :promotion promotion
     */
    public function iArchiveThePromotion(PromotionInterface $promotion): void
    {
        $this->client->customItemAction(Resources::PROMOTIONS, $promotion->getCode(), Request::METHOD_PATCH, 'archive');
        $this->client->index(Resources::PROMOTIONS);
    }

    /**
     * @When I restore the :promotion promotion
     */
    public function iRestoreThePromotion(PromotionInterface $promotion): void
    {
        $this->client->customItemAction(Resources::PROMOTIONS, $promotion->getCode(), Request::METHOD_PATCH, 'restore');
    }

    /**
     * @When I specify its :field as :value
     * @When I do not specify its :field
     * @When I :field it :value
     */
    public function iSpecifyItsAs(string $field, ?string $value = null): void
    {
        if (null !== $value) {
            $this->client->addRequestData($field, $value);
        }
    }

    /**
     * @When I set it as not applies to discounted by catalog promotion items
     */
    public function iSetItAsNotAppliesToDiscountedByCatalogPromotionItems(): void
    {
        $this->client->updateRequestData(['appliesToDiscounted' => false]);
    }

    /**
     * @When I set its usage limit to :usageLimit
     */
    public function iSetItsUsageLimitTo(int $usageLimit): void
    {
        $this->client->addRequestData('usageLimit', $usageLimit);
    }

    /**
     * @When I set it as exclusive
     */
    public function iSetItAsExclusive(): void
    {
        $this->client->addRequestData('exclusive', true);
    }

    /**
     * @When I make it coupon based
     */
    public function iMakeItCouponBased(): void
    {
        $this->client->addRequestData('couponBased', true);
    }

    /**
     * @When I set its priority to :priority
     * @When I remove its priority
     */
    public function iRemoveItsPriority(?int $priority = null): void
    {
        $this->client->addRequestData('priority', $priority);
    }

    /**
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt(string $name = ''): void
    {
        $this->client->addRequestData('name', $name);
    }

    /**
     * @When I make it applicable for the :channel channel
     */
    public function iMakeItApplicableForTheChannel(ChannelInterface $channel): void
    {
        $this->client->addRequestData('channels', [$this->iriConverter->getIriFromItem($channel)]);
    }

    /**
     * @When I make it available from :startsDate to :endsDate
     */
    public function iMakeItAvailableFromTo(\DateTimeInterface $startsDate, \DateTimeInterface $endsDate): void
    {
        $this->client->updateRequestData([
            'startsAt' => $startsDate->format('Y-m-d H:i:s'),
            'endsAt' => $endsDate->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @When I specify its label as :label in :localeCode locale
     */
    public function iSpecifyItsLabelInLocaleCode(string $label, string $localeCode): void
    {
        $data['translations'][$localeCode]['label'] = $label;

        $this->client->updateRequestData($data);
    }

    /**
     * @When I replace its label with a string exceeding the limit in :localeCode locale
     */
    public function iSpecifyItsLabelWithAStringExceedingTheLimitInLocale(string $localeCode): void
    {
        $this->iSpecifyItsLabelInLocaleCode(str_repeat('a', 256), $localeCode);
    }

    /**
     * @When /^I add the "([^"]+)" action configured with amount of "(?:€|£|\$)([^"]+)" for ("[^"]+" channel)$/
     */
    public function iAddTheActionConfiguredWithAmountForChannel(
        string $actionType,
        int $amount,
        ChannelInterface $channel,
    ): void {
        $actionTypeMapping = [
            'Order fixed discount' => FixedDiscountPromotionActionCommand::TYPE,
            'Item fixed discount' => UnitFixedDiscountPromotionActionCommand::TYPE,
        ];

        $this->addToRequestAction(
            $actionTypeMapping[$actionType],
            [
                $channel->getCode() => [
                    'amount' => $amount,
                ],
            ],
        );
    }

    /**
     * @When /^I add the "Item percentage discount" action configured with a percentage value of ("[^"]+") for ("[^"]+" channel)$/
     */
    public function iAddTheActionConfiguredWithAPercentageValueForChannel(
        float $percentage,
        ChannelInterface $channel,
    ): void {
        $this->addToRequestAction(
            UnitPercentageDiscountPromotionActionCommand::TYPE,
            [
                $channel->getCode() => [
                    'percentage' => $percentage,
                ],
            ],
        );
    }

    /**
     * @When I add the "Item percentage discount" action configured without a percentage value for :channel channel
     */
    public function iAddTheActionConfiguredWithoutAPercentageValueForChannel(ChannelInterface $channel): void
    {
        $this->addToRequestAction(
            UnitPercentageDiscountPromotionActionCommand::TYPE,
            [
                $channel->getCode() => [
                    'percentage' => null,
                ],
            ],
        );
    }

    /**
     * @When /^I add the "([^"]+)" action configured with a percentage value of ("[^"]+")$/
     * @When I add the :actionType action configured without a percentage value
     */
    public function iAddTheActionConfiguredWithAPercentageValue(string $actionType, ?float $percentage = null): void
    {
        $actionTypeMapping = [
            'Order percentage discount' => PercentageDiscountPromotionActionCommand::TYPE,
            'Shipping percentage discount' => ShippingPercentageDiscountPromotionActionCommand::TYPE,
        ];

        $this->addToRequestAction(
            $actionTypeMapping[$actionType],
            [
                'percentage' => $percentage,
            ],
        );
    }

    /**
     * @When /^it is(?:| also) configured with amount of "(?:€|£|\$)([^"]+)" for ("[^"]+" channel)$/
     */
    public function itIsConfiguredWithAmountForChannel(float $amount, ChannelInterface $channel): void
    {
        $actions = $this->getActions();
        $actions[0]['configuration'][$channel->getCode()]['amount'] = $amount;

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When /^I edit (this promotion) percentage action to have ("[^"]+")$/
     */
    public function iEditPromotionToHaveDiscount(PromotionInterface $promotion, float $percentage): void
    {
        $actions = $this->getActions();
        $actions[0]['configuration']['percentage'] = $percentage;

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When /^I specify that on ("[^"]+" channel) this action should be applied to items with price greater than "(?:€|£|\$)([^"]+)"$/
     */
    public function iAddAMinPriceFilterRangeForChannel(ChannelInterface $channel, int|string $minimum): void
    {
        $actions = $this->getActions();
        $actions[0]['configuration'][$channel->getCode()]['filters']['price_range_filter']['min'] = $minimum;

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When /^I specify that on ("[^"]+" channel) this action should be applied to items with price lesser than "(?:€|£|\$)([^"]+)"$/
     */
    public function iAddAMaxPriceFilterRangeForChannel(ChannelInterface $channel, int|string $maximum): void
    {
        $actions = $this->getActions();
        $actions[0]['configuration'][$channel->getCode()]['filters']['price_range_filter']['max'] = $maximum;

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When /^I specify that on ("[^"]+" channel) this action should be applied to items with price between "(?:€|£|\$)([^"]+)" and "(?:€|£|\$)([^"]+)"$/
     */
    public function iAddAMinMaxPriceFilterRangeForChannel(ChannelInterface $channel, int $minimum, int $maximum): void
    {
        $this->iAddAMinPriceFilterRangeForChannel($channel, $minimum);
        $this->iAddAMaxPriceFilterRangeForChannel($channel, $maximum);
    }

    /**
     * @When I specify that this action should be applied to items from :taxon category
     */
    public function iSpecifyThatThisActionShouldBeAppliedToItemsFromCategory(TaxonInterface $taxon): void
    {
        $actions = $this->getActions();
        $channelCode = key($actions[0]['configuration']);
        $actions[0]['configuration'][$channelCode]['filters']['taxons_filter']['taxons'] = [$taxon->getCode()];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When I specify that this action should be applied to the :product product
     */
    public function iSpecifyThatThisActionShouldBeAppliedToTheProduct(ProductInterface $product): void
    {
        $actions = $this->getActions();
        $channelCode = key($actions[0]['configuration']);
        $actions[0]['configuration'][$channelCode]['filters']['products_filter']['products'] = [$product->getCode()];

        $this->client->addRequestData('actions', $actions);
    }

    /**
     * @When /^I add the "Has at least one from taxons" rule configured with ("[^"]+" taxon)$/
     * @When /^I add the "Has at least one from taxons" rule configured with ("[^"]+" taxon) and ("[^"]+" taxon)$/
     */
    public function iAddTheHasTaxonRuleConfiguredWith(TaxonInterface ...$taxons): void
    {
        $this->addToRequestRule(
            HasTaxonRuleChecker::TYPE,
            [
                'taxons' => array_map(fn (TaxonInterface $taxon): string => $taxon->getCode(), $taxons),
            ],
        );
    }

    /**
     * @When /^I add the "Total price of items from taxon" rule configured with ("[^"]+" taxon) and ("[^"]+") amount for ("[^"]+" channel)$/
     */
    public function iAddTheRuleConfiguredWith(TaxonInterface $taxon, int $amount, ChannelInterface $channel): void
    {
        $this->addToRequestRule(
            TotalOfItemsFromTaxonRuleChecker::TYPE,
            [
                $channel->getCode() => [
                    'amount' => $amount,
                    'taxon' => $taxon->getCode(),
                ],
            ],
        );
    }

    /**
     * @When /^I add the "Item total" rule configured with ("[^"]+") amount for ("[^"]+" channel) and ("[^"]+") amount for ("[^"]+" channel)$/
     */
    public function iAddTheItemTotalRuleConfiguredWithTwoChannel(
        int $firstAmount,
        ChannelInterface $firstChannel,
        int $secondAmount,
        ChannelInterface $secondChannel,
    ): void {
        $this->addToRequestRule(
            ItemTotalRuleChecker::TYPE,
            [
                $firstChannel->getCode() => [
                    'amount' => $firstAmount,
                ],
                $secondChannel->getCode() => [
                    'amount' => $secondAmount,
                ],
            ],
        );
    }

    /**
     * @When I add the "Contains product" rule configured with the :product product
     */
    public function iAddTheRuleConfiguredWithTheProduct(ProductInterface $product): void
    {
        $this->addToRequestRule(
            ContainsProductRuleChecker::TYPE,
            [
                'product_code' => $product->getCode(),
            ],
        );
    }

    /**
     * @When I add the "Customer group" rule for :customerGroup group
     */
    public function iAddTheCustomerGroupRuleConfiguredForGroup(CustomerGroupInterface $customerGroup): void
    {
        $this->addToRequestRule(
            CustomerGroupRuleChecker::TYPE,
            [
                'group_code' => $customerGroup->getCode(),
            ],
        );
    }

    /**
     * @When I filter promotions by coupon code equal :value
     */
    public function iFilterPromotionsByCouponCodeEqual(string $value): void
    {
        $this->client->addFilter('coupons.code', $value);
        $this->client->filter();
    }

    /**
     * @When I filter archival promotions
     */
    public function iFilterArchivalPromotions(): void
    {
        $this->client->addFilter('exists[archivedAt]', true);
        $this->client->filter();
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
     * @Then I should see a single promotion in the list
     * @Then there should be :amount promotions
     */
    public function thereShouldBePromotion(int $amount = 1): void
    {
        Assert::same(
            count($this->responseChecker->getCollection($this->client->getLastResponse())),
            $amount,
        );
    }

    /**
     * @Then the :promotionName promotion should appear in the registry
     * @Then the :promotionName promotion should exist in the registry
     * @Then promotion :promotionName should still exist in the registry
     * @Then this promotion should still be named :promotionName
     */
    public function thePromotionShouldAppearInTheRegistry(string $promotionName): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::PROMOTIONS), 'name', $promotionName),
            sprintf('Promotion with name %s does not exist', $promotionName),
        );
    }

    /**
     * @Then I should see the promotion :promotionName in the list
     */
    public function iShouldSeeThePromotionInTheList(string $promotionName): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'name', $promotionName),
            sprintf('Promotion with name %s does not exist', $promotionName),
        );
    }

    /**
     * @Then I should not see the promotion :promotionName in the list
     */
    public function iShouldNotSeeThePromotionInTheList(string $promotionName): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->getLastResponse(), 'name', $promotionName),
            sprintf('Promotion with name %s does not exist', $promotionName),
        );
    }

    /**
     * @Then /^(this promotion) should be coupon based$/
     */
    public function thisPromotionShouldBeCouponBased(PromotionInterface $promotion): void
    {
        $returnedPromotion = current($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'name',
            $promotion->getName(),
        ));

        Assert::true(
            $returnedPromotion['couponBased'],
            sprintf('The promotion %s isn\'t coupon based', $promotion->getName()),
        );
    }

    /**
     * @Then /^I should be able to manage coupons for (this promotion)$/
     */
    public function iShouldBeAbleToManageCouponsForThisPromotion(PromotionInterface $promotion): void
    {
        $returnedPromotion = current($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'name',
            $promotion->getName(),
        ));

        Assert::keyExists($returnedPromotion, 'coupons');
    }

    /**
     * @When /^I delete a ("([^"]+)" promotion)$/
     * @When /^I try to delete a ("([^"]+)" promotion)$/
     */
    public function iDeletePromotion(PromotionInterface $promotion): void
    {
        $this->client->delete(Resources::PROMOTIONS, $promotion->getCode());
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Promotion still exists, but it should not',
        );
    }

    /**
     * @Then /^(this promotion) should no longer exist in the promotion registry$/
     */
    public function promotionShouldNotExistInTheRegistry(PromotionInterface $promotion): void
    {
        $response = $this->client->index(Resources::PROMOTIONS);
        $promotionName = (string) $promotion->getName();

        Assert::false(
            $this->responseChecker->hasItemWithValue($response, 'name', $promotionName),
            sprintf('Promotion with name %s still exist', $promotionName),
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true($this->responseChecker->isCreationSuccessful($this->client->getLastResponse()));
    }

    /**
     * @Then the :promotion promotion should not applies to discounted items
     */
    public function thePromotionShouldNotAppliesToDiscountedItems(PromotionInterface $promotion): void
    {
        Assert::false(
            $this->responseChecker->getValue($this->client->show(Resources::PROMOTIONS, $promotion->getCode()), 'appliesToDiscounted'),
        );
    }

    /**
     * @Then the :promotion promotion should be available to be used only :usageLimit times
     */
    public function thePromotionShouldBeAvailableToUseOnlyTimes(PromotionInterface $promotion, int $usageLimit): void
    {
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show(Resources::PROMOTIONS, $promotion->getCode()),
                'usageLimit',
                $usageLimit,
            ),
        );
    }

    /**
     * @Then the :promotion promotion should be exclusive
     */
    public function thePromotionShouldBeExclusive(PromotionInterface $promotion): void
    {
        Assert::true(
            $this->responseChecker->getValue(
                $this->client->show(Resources::PROMOTIONS, $promotion->getCode()),
                'exclusive',
            ),
        );
    }

    /**
     * @Then the :promotion promotion should be coupon based
     */
    public function thePromotionShouldBeCouponBased(PromotionInterface $promotion): void
    {
        Assert::true(
            $this->responseChecker->getValue(
                $this->client->show(Resources::PROMOTIONS, $promotion->getCode()),
                'couponBased',
            ),
        );
    }

    /**
     * @Then the :promotion promotion should be applicable for the :channel channel
     */
    public function thePromotionShouldBeApplicableForTheChannel(PromotionInterface $promotion, ChannelInterface $channel): void
    {
        Assert::true(
            $this->responseChecker->hasValueInCollection(
                $this->client->show(Resources::PROMOTIONS, $promotion->getCode()),
                'channels',
                $this->sectionAwareIriConverter->getIriFromResourceInSection($channel, 'admin'),
            ),
        );
    }

    /**
     * @When the :promotion promotion should have a label :label in :localeCode locale
     */
    public function thePromotionShouldHaveLabelInLocale(PromotionInterface $promotion, string $label, string $localeCode): void
    {
        $response = $this->client->show(Resources::PROMOTIONS, $promotion->getCode());

        Assert::true($this->responseChecker->hasTranslation($response, $localeCode, 'label', $label));
    }

    /**
     * @Then /^it should have ("[^"]+") of item percentage discount configured for ("[^"]+" channel)$/
     */
    public function itShouldHaveOfItemPercentageDiscount(float $percentage, ChannelInterface $channel): void
    {
        $actions = $this->responseChecker->getValue($this->client->getLastResponse(), 'actions');
        foreach ($actions as $action) {
            if ($action['type'] === 'unit_percentage_discount') {
                Assert::same((float) $action['configuration'][$channel->getCode()]['percentage'], $percentage);
            }
        }
    }

    /**
     * @Then /^it should have ("[^"]+") of order percentage discount$/
     */
    public function itShouldHaveOfOrderPercentageDiscount(float $percentage): void
    {
        $actions = $this->responseChecker->getValue($this->client->getLastResponse(), 'actions');
        Assert::same((float) $actions[0]['configuration']['percentage'], $percentage);
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
     * @Then the :promotion promotion should be available from :startsDate to :endsDate
     */
    public function thePromotionShouldBeAvailableFromTo(
        PromotionInterface $promotion,
        \DateTimeInterface $startsDate,
        \DateTimeInterface $endsDate,
    ): void {
        Assert::true(
            $this->responseChecker->hasItemWithValues(
                $this->client->index(Resources::PROMOTIONS),
                [
                    'name' => $promotion->getName(),
                    'startsAt' => $startsDate->format('Y-m-d H:i:s'),
                    'endsAt' => $endsDate->format('Y-m-d H:i:s'),
                ],
            ),
        );
    }

    /**
     * @Then I should be able to modify a :promotion promotion
     */
    public function iShouldBeAbleToModifyAPromotion(PromotionInterface $promotion): void
    {
        $this->iWantToModifyAPromotion($promotion);
        $this->client->updateRequestData(['name' => 'NEW_NAME']);

        Assert::true($this->responseChecker->hasValue($this->client->update(), 'name', 'NEW_NAME'));
    }

    /**
     * @Then the :promotion promotion should have priority :priority
     */
    public function thePromotionsShouldHavePriority(PromotionInterface $promotion, int $priority): void
    {
        Assert::true(
            $this->responseChecker->hasItemWithValues(
                $this->client->index(Resources::PROMOTIONS),
                [
                    'name' => $promotion->getName(),
                    'priority' => $priority,
                ],
            ),
        );
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedThatItIsIUseAndCannotBeDeleted(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot delete, the promotion is in use.',
        );
    }

    /**
     * @Then I should be notified that promotion with this code already exists
     */
    public function iShouldBeNotifiedThatPromotionWithThisCodeAlreadyExists(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false($this->responseChecker->isCreationSuccessful($response));
        Assert::same(
            $this->responseChecker->getError($response),
            'code: The promotion with given code already exists.',
        );
    }

    /**
     * @Then there should still be only one promotion with :element :value
     */
    public function thereShouldStillBeOnlyOnePromotionWith(string $element, string $value): void
    {
        Assert::count(
            $this->responseChecker->getCollectionItemsWithValue($this->client->index(Resources::PROMOTIONS), $element, $value),
            1,
        );
    }

    /**
     * @Then promotion with :element :value should not be added
     */
    public function promotionWithElementValueShouldNotBeAdded(string $element, string $value): void
    {
        Assert::false($this->responseChecker->hasItemWithValue($this->client->index(Resources::PROMOTIONS), $element, $value));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s: Please enter promotion %s.', $element, $element),
        );
    }

    /**
     * @Then I should be notified that promotion cannot end before it starts
     */
    public function iShouldBeNotifiedThatPromotionCannotEndBeforeItsEvenStarts(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'endsAt: End date cannot be set prior start date.',
        );
    }

    /**
     * @Then I should be notified that promotion label in :localeCode locale is too long
     */
    public function iShouldBeNotifiedThatPromotionLabelIsTooLong(string $localeCode): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('translations[%s].label: This value is too long. It should have 255 characters or less.', $localeCode),
        );
    }

    /**
     * @Then I should be notified that this value should not be blank
     */
    public function iShouldBeNotifiedThatThisValueShouldNotBeBlank(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'This value should not be blank.',
        );
    }

    /**
     * @Then I should be notified that a percentage discount value must be between 0% and 100%
     * @Then I should be notified that a percentage discount value must be at least 0%
     * @Then I should be notified that the maximum value of a percentage discount is 100%
     */
    public function iShouldBeNotifiedThatPercentageDiscountShouldBeBetween(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'The percentage discount must be between 0% and 100%.',
        );
    }

    /**
     * @Then I should be notified that a minimum value should be a numeric value
     */
    public function iShouldBeNotifiedThatAMinimalValueShouldBeNumeric(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            '[min]: This value should be of type numeric.',
        );
    }

    /**
     * @Then I should be notified that a maximum value should be a numeric value
     */
    public function iShouldBeNotifiedThatAMaximumValueShouldBeNumeric(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            '[max]: This value should be of type numeric.',
        );
    }

    /**
     * @Then I should see :count promotions on the list
     * @Then I should see a single promotion on the list
     */
    public function iShouldSeePromotionInTheList(int $count = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $count);
    }

    /**
     * @Then /^the (first|last) promotion on the list should have ([^"]+) "([^"]+)"$/
     */
    public function theFirstPromotionOnTheListShouldHave(string $togglePosition, string $field, string $value): void
    {
        $items = $this->responseChecker->getValue($this->client->getLastResponse(), 'hydra:member');
        if ('first' === $togglePosition) {
            $item = reset($items);
        } else {
            $item = end($items);
        }

        Assert::same($item[$field], $value);
    }

    /**
     * @Then the promotion :promotion should be used :usage time(s)
     * @Then the promotion :promotion should not be used
     */
    public function thePromotionShouldBeUsedTime(PromotionInterface $promotion, int $usage = 0): void
    {
        $returnedPromotion = current($this->responseChecker->getCollectionItemsWithValue(
            $this->client->getLastResponse(),
            'code',
            $promotion->getCode(),
        ));

        Assert::same(
            $returnedPromotion['used'],
            $usage,
            sprintf('The promotion %s has been used %s times', $promotion->getName(), $returnedPromotion['used']),
        );
    }

    /**
     * @Then I should be viewing non archival promotions
     */
    public function iShouldBeViewingNonArchivalPromotions(): void
    {
        $this->client->index(Resources::PROMOTIONS);
    }

    private function addToRequestAction(string $type, array $configuration): void
    {
        $data['actions'][] = [
            'type' => $type,
            'configuration' => $configuration,
        ];

        $this->client->updateRequestData($data);
    }

    private function getActions(): array
    {
        return $this->client->getContent()['actions'];
    }

    private function addToRequestRule(string $type, array $configuration): void
    {
        $data['rules'][] = [
            'type' => $type,
            'configuration' => $configuration,
        ];

        $this->client->updateRequestData($data);
    }
}
