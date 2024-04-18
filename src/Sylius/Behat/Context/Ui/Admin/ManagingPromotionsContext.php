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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\Context\Ui\Admin\Helper\ValidationTrait;
use Sylius\Behat\Element\Admin\Promotion\FormElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as IndexPageCouponInterface;
use Sylius\Behat\Page\Admin\Promotion\CreatePageInterface;
use Sylius\Behat\Page\Admin\Promotion\IndexPageInterface;
use Sylius\Behat\Page\Admin\Promotion\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Webmozart\Assert\Assert;

final class ManagingPromotionsContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private IndexPageInterface $indexPage,
        private IndexPageCouponInterface $indexCouponPage,
        private CreatePageInterface $createPage,
        private UpdatePageInterface $updatePage,
        private CurrentPageResolverInterface $currentPageResolver,
        private NotificationCheckerInterface $notificationChecker,
        private FormElementInterface $formElement,
    ) {
    }

    /**
     * @When I create a new promotion
     * @When I want to create a new promotion
     */
    public function iWantToCreateANewPromotion(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I want to browse promotions
     * @When I browse promotions
     */
    public function iWantToBrowsePromotions(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->createPage->specifyCode($code ?? '');
    }

    /**
     * @When I name it :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt($name = null)
    {
        $this->createPage->nameIt($name ?? '');
    }

    /**
     * @When I set its priority to :priority
     * @When I remove its priority
     */
    public function iRemoveItsPriority(?int $priority = null): void
    {
        $this->formElement->setPriority($priority);
    }

    /**
     * @Then the :promotionName promotion should appear in the registry
     * @Then the :promotionName promotion should exist in the registry
     * @Then this promotion should still be named :promotionName
     * @Then promotion :promotionName should still exist in the registry
     */
    public function thePromotionShouldAppearInTheRegistry(string $promotionName): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $promotionName]));
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @When I specify its label as :label in :localeCode locale
     */
    public function iSpecifyItsLabelInLocaleCode(string $label, string $localeCode): void
    {
        $this->formElement->setLabel($label, $localeCode);
    }

    /**
     * @When I replace its label with a string exceeding the limit in :localeCode locale
     */
    public function iSpecifyItsLabelWithAStringExceedingTheLimitInLocale(string $localeCode): void
    {
        $this->formElement->setLabel(str_repeat('a', 256), $localeCode);
    }

    /**
     * @When the :promotion promotion should have a label :label in :localeCode locale
     */
    public function thePromotionShouldHaveLabelInLocale(PromotionInterface $promotion, string $label, string $localeCode): void
    {
        $this->updatePage->open(['id' => $promotion->getId()]);
        $this->formElement->hasLabel($label, $localeCode);
    }

    /**
     * @When I add the "Has at least one from taxons" rule configured with :firstTaxon taxon
     * @When I add the "Has at least one from taxons" rule configured with :firstTaxon taxon and :secondTaxon taxon
     */
    public function iAddTheHasTaxonRuleConfiguredWith(string ...$taxons): void
    {
        $this->formElement->addRule('Has at least one from taxons');

        $this->formElement->selectAutocompleteRuleOptions($taxons);
    }

    /**
     * @When /^I add the "Total price of items from taxon" rule configured with "([^"]+)" taxon and "(?:€|£|\$)([^"]+)" amount for ("[^"]+" channel)$/
     */
    public function iAddTheRuleConfiguredWith(string $taxonName, $amount, ChannelInterface $channel): void
    {
        $this->formElement->addRule('Total price of items from taxon');
        $this->formElement->selectAutocompleteRuleOptions([$taxonName], $channel->getCode());
        $this->formElement->fillRuleOptionForChannel($channel->getCode(), 'Amount', $amount);
    }

    /**
     * @When /^I add the "Item total" rule configured with "(?:€|£|\$)([^"]+)" amount for ("[^"]+" channel) and "(?:€|£|\$)([^"]+)" amount for ("[^"]+" channel)$/
     */
    public function iAddTheItemTotalRuleConfiguredWithTwoChannel(
        $firstAmount,
        ChannelInterface $firstChannel,
        $secondAmount,
        ChannelInterface $secondChannel,
    ) {
        $this->formElement->addRule('Item total');
        $this->formElement->fillRuleOptionForChannel($firstChannel->getCode(), 'Amount', $firstAmount);
        $this->formElement->fillRuleOptionForChannel($secondChannel->getCode(), 'Amount', $secondAmount);
    }

    /**
     * @When /^I add the "([^"]+)" action configured with amount of "(?:€|£|\$)([^"]+)" for ("[^"]+" channel)$/
     */
    public function iAddTheActionConfiguredWithAmountForChannel($actionType, $amount, ChannelInterface $channel)
    {
        $this->formElement->addAction($actionType);
        $this->formElement->fillActionOptionForChannel($channel->getCode(), 'Amount', $amount);
    }

    /**
     * @When /^it is(?:| also) configured with amount of "(?:€|£|\$)([^"]+)" for ("[^"]+" channel)$/
     */
    public function itIsConfiguredWithAmountForChannel($amount, ChannelInterface $channel)
    {
        $this->formElement->fillActionOptionForChannel($channel->getCode(), 'Amount', $amount);
    }

    /**
     * @When /^I specify that on ("[^"]+" channel) this action should be applied to items with price greater than "(?:€|£|\$)([^"]+)"$/
     */
    public function iAddAMinPriceFilterRangeForChannel(ChannelInterface $channel, $minimum)
    {
        $this->formElement->fillActionOptionForChannel($channel->getCode(), 'Min', $minimum);
    }

    /**
     * @When /^I specify that on ("[^"]+" channel) this action should be applied to items with price lesser than "(?:€|£|\$)([^"]+)"$/
     */
    public function iAddAMaxPriceFilterRangeForChannel(ChannelInterface $channel, $maximum)
    {
        $this->formElement->fillActionOptionForChannel($channel->getCode(), 'Max', $maximum);
    }

    /**
     * @When /^I specify that on ("[^"]+" channel) this action should be applied to items with price between "(?:€|£|\$)([^"]+)" and "(?:€|£|\$)([^"]+)"$/
     */
    public function iAddAMinMaxPriceFilterRangeForChannel(ChannelInterface $channel, $minimum, $maximum)
    {
        $this->iAddAMinPriceFilterRangeForChannel($channel, $minimum);
        $this->iAddAMaxPriceFilterRangeForChannel($channel, $maximum);
    }

    /**
     * @When I specify that this action should be applied to items from :taxonName category for :channel channel
     */
    public function iSpecifyThatThisActionShouldBeAppliedToItemsFromCategory(string $taxonName, ChannelInterface $channel): void
    {
        $this->formElement->selectAutocompleteActionFilterOptions([$taxonName], $channel->getCode(), 'taxons');
    }

    /**
     * @When /^I add the "([^"]+)" action configured with a percentage value of "(?:|-)([^"]+)%" for ("[^"]+" channel)$/
     */
    public function iAddTheActionConfiguredWithAPercentageValueForChannel(
        string $actionType,
        string $percentage,
        ChannelInterface $channel,
    ): void {
        $this->formElement->addAction($actionType);
        $this->formElement->fillActionOptionForChannel($channel->getCode(), 'Percentage', $percentage);
    }

    /**
     * @When I add the :actionType action configured without a percentage value for :channel channel
     */
    public function iAddTheActionConfiguredWithoutAPercentageValueForChannel(
        string $actionType,
        ChannelInterface $channel,
    ): void {
        $this->formElement->addAction($actionType);
        $this->formElement->fillActionOptionForChannel($channel->getCode(), 'Percentage', '');
    }

    /**
     * @When /^I add the "([^"]+)" action configured with a percentage value of "(?:|-)([^"]+)%"$/
     * @When I add the :actionType action configured without a percentage value
     */
    public function iAddTheActionConfiguredWithAPercentageValue($actionType, $percentage = null)
    {
        $this->formElement->addAction($actionType);
        $this->formElement->fillActionOption('Percentage', $percentage ?? '');
    }

    /**
     * @When I add the "Customer group" rule for :customerGroupName group
     */
    public function iAddTheCustomerGroupRuleConfiguredForGroup($customerGroupName)
    {
        $this->formElement->addRule('Customer group');
        $this->formElement->selectRuleOption('Customer group', $customerGroupName);
    }

    /**
     * @When I check (also) the :promotionName promotion
     */
    public function iCheckThePromotion(string $promotionName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $promotionName]);
    }

    /**
     * @When I remove its last rule
     */
    public function iRemoveItsLastRule(): void
    {
        $this->formElement->removeLastRule();
    }

    /**
     * @When I remove its last action
     */
    public function iRemoveItsLastAction(): void
    {
        $this->formElement->removeLastAction();
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @When I archive the :promotionName promotion
     */
    public function iArchiveThePromotion(string $promotionName): void
    {
        $actions = $this->indexPage->getActionsForResource(['name' => $promotionName]);
        $actions->pressButton('Archive');
    }

    /**
     * @When I restore the :promotionName promotion
     */
    public function iRestoreThePromotion(string $promotionName): void
    {
        $actions = $this->indexPage->getActionsForResource(['name' => $promotionName]);
        $actions->pressButton('Restore');
    }

    /**
     * @When I filter archival promotions
     */
    public function iFilterArchivalPromotions(): void
    {
        $this->indexPage->chooseArchival('Yes');
        $this->indexPage->filter();
    }

    /**
     * @Then I should see a single promotion in the list
     * @Then there should be :amount promotions
     */
    public function thereShouldBePromotion(int $amount = 1): void
    {
        Assert::same($this->indexPage->countItems(), $amount);
    }

    /**
     * @Then /^(this promotion) should be coupon based$/
     */
    public function thisPromotionShouldBeCouponBased(PromotionInterface $promotion)
    {
        Assert::true($this->indexPage->isCouponBasedFor($promotion));
    }

    /**
     * @Then /^I should be able to manage coupons for (this promotion)$/
     */
    public function iShouldBeAbleToManageCouponsForThisPromotion(PromotionInterface $promotion)
    {
        Assert::true($this->indexPage->isAbleToManageCouponsFor($promotion));
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter promotion %s.', $element));
    }

    /**
     * @Then I should be notified that a :element value should be a numeric value
     */
    public function iShouldBeNotifiedThatAMinimalValueShouldBeNumeric($element)
    {
        $this->assertFieldValidationMessage($element, 'Please enter a valid money amount.');
    }

    /**
     * @Then I should be notified that promotion with this code already exists
     */
    public function iShouldBeNotifiedThatPromotionWithThisCodeAlreadyExists()
    {
        Assert::same($this->formElement->getValidationMessage('code'), 'The promotion with given code already exists.');
    }

    /**
     * @Then promotion with :element :name should not be added
     */
    public function promotionWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $name]));
    }

    /**
     * @Then there should still be only one promotion with :element :value
     */
    public function thereShouldStillBeOnlyOnePromotionWith($element, $value)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @When I set its usage limit to :usageLimit
     */
    public function iSetItsUsageLimitTo(int $usageLimit): void
    {
        $this->formElement->setUsageLimit($usageLimit);
    }

    /**
     * @Then the :promotion promotion should be available to be used only :usageLimit times
     */
    public function thePromotionShouldBeAvailableToUseOnlyTimes(PromotionInterface $promotion, int $usageLimit): void
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true($this->updatePage->hasResourceValues(['usage_limit' => $usageLimit]));
    }

    /**
     * @When I set it as exclusive
     */
    public function iSetItAsExclusive(): void
    {
        $this->formElement->makeExclusive();
    }

    /**
     * @When I set it as not applies to discounted by catalog promotion items
     */
    public function iSetItAsNotAppliesToDiscountedByCatalogPromotionItems(): void
    {
        $this->formElement->makeNotAppliesToDiscountedItem();
    }

    /**
     * @Then the :promotion promotion should be exclusive
     */
    public function thePromotionShouldBeExclusive(PromotionInterface $promotion): void
    {
        $this->assertIfFieldIsTrue($promotion, 'exclusive');
    }

    /**
     * @Then the :promotion promotion should not applies to discounted items
     */
    public function thePromotionShouldNotAppliesToDiscountedItems(PromotionInterface $promotion): void
    {
        $this->assertIfFieldIsFalse($promotion, 'applies_to_discounted');
    }

    /**
     * @When I make it coupon based
     */
    public function iMakeItCouponBased(): void
    {
        $this->formElement->makeCouponBased();
    }

    /**
     * @Then the :promotion promotion should be coupon based
     */
    public function thePromotionShouldBeCouponBased(PromotionInterface $promotion): void
    {
        $this->assertIfFieldIsTrue($promotion, 'coupon_based');
    }

    /**
     * @When I make it applicable for the :channelName channel
     */
    public function iMakeItApplicableForTheChannel(string $channelName): void
    {
        $this->formElement->checkChannel($channelName);
    }

    /**
     * @Then the :promotion promotion should be applicable for the :channelName channel
     */
    public function thePromotionShouldBeApplicableForTheChannel(PromotionInterface $promotion, string $channelName): void
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true($this->updatePage->checkChannelsState($channelName));
    }

    /**
     * @When I want to modify a :promotion promotion
     * @When /^I want to modify (this promotion)$/
     * @When I modify a :promotion promotion
     */
    public function iWantToModifyAPromotion(PromotionInterface $promotion): void
    {
        $this->updatePage->open(['id' => $promotion->getId()]);
    }

    /**
     * @When /^I edit (this promotion) percentage action to have "([^"]+)%"$/
     */
    public function iEditPromotionToHaveDiscount(PromotionInterface $promotion, string $amount): void
    {
        $this->updatePage->open(['id' => $promotion->getId()]);
        $this->updatePage->specifyOrderPercentageDiscountActionValue($amount);
        $this->updatePage->saveChanges();
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When /^I delete a ("([^"]+)" promotion)$/
     * @When /^I try to delete a ("([^"]+)" promotion)$/
     */
    public function iDeletePromotion(PromotionInterface $promotion)
    {
        $this->sharedStorage->set('promotion', $promotion);

        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $promotion->getName()]);
    }

    /**
     * @Then /^(this promotion) should no longer exist in the promotion registry$/
     */
    public function promotionShouldNotExistInTheRegistry(PromotionInterface $promotion)
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $promotion->getCode()]));
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete, the Promotion is in use.',
            NotificationType::failure(),
        );
    }

    /**
     * @When I make it available from :startsDate to :endsDate
     */
    public function iMakeItAvailableFromTo(\DateTimeInterface $startsDate, \DateTimeInterface $endsDate): void
    {
        $this->formElement->setStartsAt($startsDate);
        $this->formElement->setEndsAt($endsDate);
    }

    /**
     * @Then the :promotion promotion should be available from :startsDate to :endsDate
     */
    public function thePromotionShouldBeAvailableFromTo(PromotionInterface $promotion, \DateTimeInterface $startsDate, \DateTimeInterface $endsDate)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true($this->updatePage->hasStartsAt($startsDate));
        Assert::true($this->updatePage->hasEndsAt($endsDate));
    }

    /**
     * @Then I should be notified that promotion cannot end before it starts
     */
    public function iShouldBeNotifiedThatPromotionCannotEndBeforeItsEvenStarts(): void
    {
        Assert::same($this->formElement->getValidationMessage('ends_at_date'), 'End date cannot be set prior start date.');
    }

    /**
     * @Then I should be notified that this value should not be blank
     */
    public function iShouldBeNotifiedThatThisValueShouldNotBeBlank(): void
    {
        Assert::same(
            $this->formElement->getValidationMessageForAction(),
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
        Assert::same(
            $this->formElement->getValidationMessageForAction(),
            'The percentage discount must be between 0% and 100%.',
        );
    }

    /**
     * @Then the promotion :promotion should be used :usage time(s)
     * @Then the promotion :promotion should not be used
     */
    public function thePromotionShouldBeUsedTime(PromotionInterface $promotion, $usage = 0)
    {
        Assert::same(
            (int) $usage,
            $this->indexPage->getUsageNumber($promotion),
            'Promotion should be used %s times, but is %2$s.',
        );
    }

    /**
     * @When I add the "Contains product" rule configured with the :productName product
     */
    public function iAddTheRuleConfiguredWithTheProduct(string $productName): void
    {
        $this->formElement->addRule('Contains product');
        $this->formElement->selectAutocompleteRuleOptions([$productName]);
    }

    /**
     * @When I specify that this action should be applied to the :productName product for :channel channel
     */
    public function iSpecifyThatThisActionShouldBeAppliedToTheProduct(string $productName, ChannelInterface $channel): void
    {
        $this->formElement->selectAutocompleteActionFilterOptions([$productName], $channel->getCode(), 'products');
    }

    /**
     * @Then I should see :count promotions on the list
     */
    public function iShouldSeePromotionsOnTheList($count)
    {
        $actualCount = $this->indexPage->countItems();

        Assert::same(
            (int) $count,
            $actualCount,
            'There should be %s promotion, but there\'s %2$s.',
        );
    }

    /**
     * @Then the first promotion on the list should have :field :value
     */
    public function theFirstPromotionOnTheListShouldHave($field, $value)
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = reset($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected first promotion\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue),
        );
    }

    /**
     * @Then the last promotion on the list should have :field :value
     */
    public function theLastPromotionOnTheListShouldHave($field, $value)
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = end($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected last promotion\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue),
        );
    }

    /**
     * @Given the :promotion promotion should have priority :priority
     */
    public function thePromotionsShouldHavePriority(PromotionInterface $promotion, int $priority)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::same($this->formElement->getPriority(), $priority);
    }

    /**
     * @When I want to manage this promotion coupons
     */
    public function iWantToManageThisPromotionSCoupons(): void
    {
        $this->updatePage->manageCoupons();
    }

    /**
     * @Then I should not be able to access coupons management page
     */
    public function iShouldNotBeAbleToAccessCouponsManagementPage(): void
    {
        Assert::false($this->updatePage->isCouponManagementAvailable());
    }

    /**
     * @Then /^I should be on (this promotion)'s coupons management page$/
     */
    public function iShouldBeOnThisPromotionSCouponsManagementPage(PromotionInterface $promotion): void
    {
        Assert::true($this->indexCouponPage->isOpen(['promotionId' => $promotion->getId()]));
    }

    /**
     * @Then I should be able to modify a :promotion promotion
     */
    public function iShouldBeAbleToModifyAPromotion(PromotionInterface $promotion): void
    {
        $this->iWantToModifyAPromotion($promotion);
        $this->updatePage->saveChanges();
    }

    /**
     * @Then the :promotion promotion should have :ruleName rule configured
     */
    public function thePromotionShouldHaveRuleConfigured(PromotionInterface $promotion, string $ruleName): void
    {
        $this->iWantToModifyAPromotion($promotion);
        $this->updatePage->saveChanges();

        Assert::true($this->updatePage->hasRule($ruleName));
    }

    /**
     * @Then the :promotion promotion should not have any rule configured
     */
    public function thePromotionShouldNotHaveAnyRuleConfigured(PromotionInterface $promotion): void
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::false($this->updatePage->hasAnyRule());
    }

    /**
     * @When /^I filter promotions by coupon code equal "([^"]+)"/
     */
    public function iFilterPromotionsByCouponCodeEqual(string $value): void
    {
        $this->indexPage->specifyFilterType('coupon_code', 'equal');
        $this->indexPage->specifyFilterValue('coupon_code', $value);

        $this->indexPage->filter();
    }

    /**
     * @When I add a new rule
     */
    public function iAddANewRule()
    {
        $this->formElement->addRule(null);
    }

    /**
     * @When I add a new action
     */
    public function iAddANewAction(): void
    {
        $this->formElement->addAction(null);
    }

    /**
     * @When /^I remove the discount (amount|percentage) for ("[^"]+" channel)$/
     */
    public function iRemoveTheDiscountForChannel(string $field, ChannelInterface $channel): void
    {
        $this->updatePage->removeActionFieldValue($channel->getCode(), $field);
    }

    /**
     * @When I remove the rule amount for :channel channel
     */
    public function iRemoveTheRuleAmountForChannel(ChannelInterface $channel): void
    {
        $this->updatePage->removeRuleAmount($channel->getCode());
    }

    /**
     * @Then I should see the rule configuration form
     */
    public function iShouldSeeTheRuleConfigurationForm(): void
    {
        Assert::true($this->formElement->checkIfRuleConfigurationFormIsVisible(), 'Cart promotion rule configuration form is not visible.');
    }

    /**
     * @Then I should not see the rule configuration form
     */
    public function iShouldNotSeeTheRuleConfigurationForm(): void
    {
        Assert::false($this->formElement->checkIfRuleConfigurationFormIsVisible(), 'Cart promotion rule configuration form is visible.');
    }

    /**
     * @Then it should have :amount of order percentage discount
     */
    public function itShouldHaveOfOrderPercentageDiscount(string $amount): void
    {
        Assert::same($this->updatePage->getOrderPercentageDiscountActionValue(), $amount);
    }

    /**
     * @Then it should have :amount of item percentage discount configured for :channel channel
     */
    public function itShouldHaveOfItemPercentageDiscount(string $amount, ChannelInterface $channel): void
    {
        Assert::same($this->updatePage->getItemPercentageDiscountActionValue($channel->getCode()), $amount);
    }

    /**
     * @Then I should see the action configuration form
     */
    public function iShouldSeeTheActionConfigurationForm(): void
    {
        Assert::true($this->formElement->checkIfActionConfigurationFormIsVisible(), 'Cart promotion action configuration form is not visible.');
    }

    /**
     * @Then I should not see the action configuration form
     */
    public function iShouldNotSeeTheActionConfigurationForm(): void
    {
        Assert::false($this->formElement->checkIfActionConfigurationFormIsVisible(), 'Cart promotion action configuration form is visible.');
    }

    /**
     * @Then /^I should see that the rule for ("[^"]+" channel) has (\d+) validation errors?$/
     */
    public function iShouldSeeThatTheRuleForChannelHasCountValidationErrors(ChannelInterface $channel, int $count): void
    {
        Assert::same($this->updatePage->getRuleValidationErrorsCount($channel->getCode()), $count);
    }

    /**
     * @Then /^I should see that the action for ("[^"]+" channel) has (\d+) validation errors?$/
     */
    public function iShouldSeeThatTheActionForChannelHasCountValidationErrors(ChannelInterface $channel, int $count): void
    {
        Assert::same($this->updatePage->getActionValidationErrorsCount($channel->getCode()), $count);
    }

    /**
     * @Then I should be notified that :promotion promotion has been updated
     */
    public function iShouldBeNotifiedThatPromotionsHaveBeenUpdated(PromotionInterface $promotion): void
    {
        $this->notificationChecker->checkNotification(
            sprintf('Some rules of the promotions with codes %s have been updated.', $promotion->getCode()),
            NotificationType::info(),
        );
    }

    /**
     * @Then I should be notified that promotion label in :localeCode locale is too long
     */
    public function iShouldBeNotifiedThatPromotionLabelIsTooLong(string $localeCode): void
    {
        Assert::same(
            $this->formElement->getValidationMessageForTranslation('label', $localeCode),
            'This value is too long. It should have 255 characters or less.',
        );
    }

    /**
     * @Then I should see the promotion :promotionName in the list
     */
    public function iShouldSeeThePromotionInTheList(string $promotionName): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $promotionName]));
    }

    /**
     * @Then I should not see the promotion :promotionName in the list
     */
    public function iShouldNotSeeThePromotionInTheList(string $promotionName): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['name' => $promotionName]));
    }

    /**
     * @Then I should be viewing non archival promotions
     */
    public function iShouldBeViewingNonArchivalPromotions(): void
    {
        Assert::false($this->indexPage->isArchivalFilterEnabled());
    }

    /**
     * @Then the :promotion promotion should be successfully created
     */
    public function thePromotionShouldBeSuccessfullyCreated(PromotionInterface $promotion): void
    {
        $this->updatePage->verify(['id' => $promotion->getId()]);
    }

    private function assertFieldValidationMessage(string $element, string $expectedMessage)
    {
        Assert::same($this->formElement->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @param string $field
     */
    private function assertIfFieldIsTrue(PromotionInterface $promotion, $field)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true($this->updatePage->hasResourceValues([$field => 1]));
    }

    private function assertIfFieldIsFalse(PromotionInterface $promotion, $field): void
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::false($this->updatePage->hasResourceValues([$field => 1]));
    }

    protected function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);
    }
}
