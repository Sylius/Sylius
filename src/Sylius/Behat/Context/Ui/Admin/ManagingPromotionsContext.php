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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as IndexPageCouponInterface;
use Sylius\Behat\Page\Admin\Promotion\CreatePageInterface;
use Sylius\Behat\Page\Admin\Promotion\IndexPageInterface;
use Sylius\Behat\Page\Admin\Promotion\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Webmozart\Assert\Assert;

final class ManagingPromotionsContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var IndexPageInterface */
    private $indexPage;

    /** @var IndexPageCouponInterface */
    private $indexCouponPage;

    /** @var CreatePageInterface */
    private $createPage;

    /** @var UpdatePageInterface */
    private $updatePage;

    /** @var CurrentPageResolverInterface */
    private $currentPageResolver;

    /** @var NotificationCheckerInterface */
    private $notificationChecker;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $indexPage,
        IndexPageCouponInterface $indexCouponPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->indexPage = $indexPage;
        $this->indexCouponPage = $indexCouponPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
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
     * @Given I want to browse promotions
     * @When I browse promotions
     */
    public function iWantToBrowsePromotions()
    {
        $this->indexPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
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
     * @When I remove its priority
     */
    public function iRemoveItsPriority()
    {
        $this->updatePage->setPriority(null);
    }

    /**
     * @Then I should see the promotion :promotionName in the list
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
     * @When I add the "Has at least one from taxons" rule configured with :firstTaxon
     * @When I add the "Has at least one from taxons" rule configured with :firstTaxon and :secondTaxon
     */
    public function iAddTheHasTaxonRuleConfiguredWith(...$taxons)
    {
        $this->createPage->addRule('Has at least one from taxons');

        $this->createPage->selectAutocompleteRuleOption('Taxons', $taxons, true);
    }

    /**
     * @When /^I add the "Total price of items from taxon" rule configured with "([^"]+)" taxon and (?:€|£|\$)([^"]+) amount for "([^"]+)" channel$/
     */
    public function iAddTheRuleConfiguredWith($taxonName, $amount, $channelName)
    {
        $this->createPage->addRule('Total price of items from taxon');
        $this->createPage->selectAutocompleteRuleOption('Taxon', $taxonName);
        $this->createPage->fillRuleOptionForChannel($channelName, 'Amount', $amount);
    }

    /**
     * @When /^I add the "Item total" rule configured with (?:€|£|\$)([^"]+) amount for "([^"]+)" channel and (?:€|£|\$)([^"]+) amount for "([^"]+)" channel$/
     */
    public function iAddTheItemTotalRuleConfiguredWithTwoChannel(
        $firstAmount,
        $firstChannelName,
        $secondAmount,
        $secondChannelName
    ) {
        $this->createPage->addRule('Item total');
        $this->createPage->fillRuleOptionForChannel($firstChannelName, 'Amount', $firstAmount);
        $this->createPage->fillRuleOptionForChannel($secondChannelName, 'Amount', $secondAmount);
    }

    /**
     * @When /^I add the "([^"]+)" action configured with amount of "(?:€|£|\$)([^"]+)" for "([^"]+)" channel$/
     */
    public function iAddTheActionConfiguredWithAmountForChannel($actionType, $amount, $channelName)
    {
        $this->createPage->addAction($actionType);
        $this->createPage->fillActionOptionForChannel($channelName, 'Amount', $amount);
    }

    /**
     * @When /^it is(?:| also) configured with amount of "(?:€|£|\$)([^"]+)" for "([^"]+)" channel$/
     */
    public function itIsConfiguredWithAmountForChannel($amount, $channelName)
    {
        $this->createPage->fillActionOptionForChannel($channelName, 'Amount', $amount);
    }

    /**
     * @When /^I specify that on "([^"]+)" channel this action should be applied to items with price greater then "(?:€|£|\$)([^"]+)"$/
     */
    public function iAddAMinPriceFilterRangeForChannel($channelName, $minimum)
    {
        $this->createPage->fillActionOptionForChannel($channelName, 'Min', $minimum);
    }

    /**
     * @When /^I specify that on "([^"]+)" channel this action should be applied to items with price lesser then "(?:€|£|\$)([^"]+)"$/
     */
    public function iAddAMaxPriceFilterRangeForChannel($channelName, $maximum)
    {
        $this->createPage->fillActionOptionForChannel($channelName, 'Max', $maximum);
    }

    /**
     * @When /^I specify that on "([^"]+)" channel this action should be applied to items with price between "(?:€|£|\$)([^"]+)" and "(?:€|£|\$)([^"]+)"$/
     */
    public function iAddAMinMaxPriceFilterRangeForChannel($channelName, $minimum, $maximum)
    {
        $this->iAddAMinPriceFilterRangeForChannel($channelName, $minimum);
        $this->iAddAMaxPriceFilterRangeForChannel($channelName, $maximum);
    }

    /**
     * @When I specify that this action should be applied to items from :taxonName category
     */
    public function iSpecifyThatThisActionShouldBeAppliedToItemsFromCategory($taxonName)
    {
        $this->createPage->selectAutoCompleteFilterOption('Taxons', $taxonName);
    }

    /**
     * @When /^I add the "([^"]+)" action configured with a percentage value of (?:|-)([^"]+)% for ("[^"]+") channel$/
     */
    public function iAddTheActionConfiguredWithAPercentageValueForChannel(
        string $actionType,
        string $percentage = null,
        string $channelName
    ): void {
        $this->createPage->addAction($actionType);
        $this->createPage->fillActionOptionForChannel($channelName, 'Percentage', $percentage);
    }

    /**
     * @When /^I add the "([^"]+)" action configured with a percentage value of (?:|-)([^"]+)%$/
     * @When I add the :actionType action configured without a percentage value
     */
    public function iAddTheActionConfiguredWithAPercentageValue($actionType, $percentage = null)
    {
        $this->createPage->addAction($actionType);
        $this->createPage->fillActionOption('Percentage', $percentage ?? '');
    }

    /**
     * @When I add the "Customer group" rule for :customerGroupName group
     */
    public function iAddTheCustomerGroupRuleConfiguredForGroup($customerGroupName)
    {
        $this->createPage->addRule('Customer group');
        $this->createPage->selectRuleOption('Customer group', $customerGroupName);
    }

    /**
     * @When I check (also) the :promotionName promotion
     */
    public function iCheckThePromotion(string $promotionName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $promotionName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then I should see a single promotion in the list
     * @Then there should be :amount promotions
     */
    public function thereShouldBePromotion(int $amount = 1): void
    {
        Assert::same($amount, $this->indexPage->countItems());
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
        $this->assertFieldValidationMessage($element, 'This value is not valid.');
    }

    /**
     * @Then I should be notified that promotion with this code already exists
     */
    public function iShouldBeNotifiedThatPromotionWithThisCodeAlreadyExists()
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The promotion with given code already exists.');
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
    public function iSetItsUsageLimitTo($usageLimit)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->fillUsageLimit($usageLimit);
    }

    /**
     * @Then the :promotion promotion should be available to be used only :usageLimit times
     */
    public function thePromotionShouldBeAvailableToUseOnlyTimes(PromotionInterface $promotion, $usageLimit)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true($this->updatePage->hasResourceValues(['usage_limit' => $usageLimit]));
    }

    /**
     * @When I make it exclusive
     */
    public function iMakeItExclusive()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->makeExclusive();
    }

    /**
     * @Then the :promotion promotion should be exclusive
     */
    public function thePromotionShouldBeExclusive(PromotionInterface $promotion)
    {
        $this->assertIfFieldIsTrue($promotion, 'exclusive');
    }

    /**
     * @When I make it coupon based
     */
    public function iMakeItCouponBased()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->checkCouponBased();
    }

    /**
     * @Then the :promotion promotion should be coupon based
     */
    public function thePromotionShouldBeCouponBased(PromotionInterface $promotion)
    {
        $this->assertIfFieldIsTrue($promotion, 'coupon_based');
    }

    /**
     * @When I make it applicable for the :channelName channel
     */
    public function iMakeItApplicableForTheChannel($channelName)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->checkChannel($channelName);
    }

    /**
     * @Then the :promotion promotion should be applicable for the :channelName channel
     */
    public function thePromotionShouldBeApplicableForTheChannel(PromotionInterface $promotion, $channelName)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true($this->updatePage->checkChannelsState($channelName));
    }

    /**
     * @Given I want to modify a :promotion promotion
     * @Given /^I want to modify (this promotion)$/
     * @When I modify a :promotion promotion
     */
    public function iWantToModifyAPromotion(PromotionInterface $promotion): void
    {
        $this->updatePage->open(['id' => $promotion->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
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
            'Cannot delete, the promotion is in use.',
            NotificationType::failure()
        );
    }

    /**
     * @When I make it available from :startsDate to :endsDate
     */
    public function iMakeItAvailableFromTo(\DateTimeInterface $startsDate, \DateTimeInterface $endsDate)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setStartsAt($startsDate);
        $currentPage->setEndsAt($endsDate);
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
     * @Then I should be notified that promotion cannot end before it start
     */
    public function iShouldBeNotifiedThatPromotionCannotEndBeforeItsEvenStart()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('ends_at'), 'End date cannot be set prior start date.');
    }

    /**
     * @Then I should be notified that this value should not be blank
     */
    public function iShouldBeNotifiedThatThisValueShouldNotBeBlank()
    {
        Assert::same(
            $this->createPage->getValidationMessageForAction(),
            'This value should not be blank.'
        );
    }

    /**
     * @Then I should be notified that the maximum value of a percentage discount is 100%
     */
    public function iShouldBeNotifiedThatTheMaximumValueOfAPercentageDiscountIs100()
    {
        Assert::same(
            $this->createPage->getValidationMessageForAction(),
            'The maximum value of a percentage discount is 100%.'
        );
    }

    /**
     * @Then I should be notified that a percentage discount value must be at least 0%
     */
    public function iShouldBeNotifiedThatAPercentageDiscountValueMustBeAtLeast0()
    {
        Assert::same(
            $this->createPage->getValidationMessageForAction(),
            'The value of a percentage discount must be at least 0%.'
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
            'Promotion should be used %s times, but is %2$s.'
        );
    }

    /**
     * @When I add the "Contains product" rule configured with the :productName product
     */
    public function iAddTheRuleConfiguredWithTheProduct($productName)
    {
        $this->createPage->addRule('Contains product');
        $this->createPage->selectAutocompleteRuleOption('Product code', $productName);
    }

    /**
     * @When I specify that this action should be applied to the :productName product
     */
    public function iSpecifyThatThisActionShouldBeAppliedToTheProduct($productName)
    {
        $this->createPage->selectAutoCompleteFilterOption('Products', $productName);
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
            'There should be %s promotion, but there\'s %2$s.'
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
            sprintf('Expected first promotion\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
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
            sprintf('Expected last promotion\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @Given the :promotion promotion should have priority :priority
     */
    public function thePromotionsShouldHavePriority(PromotionInterface $promotion, int $priority)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::same($this->updatePage->getPriority(), $priority);
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
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @param string $field
     */
    private function assertIfFieldIsTrue(PromotionInterface $promotion, $field)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true($this->updatePage->hasResourceValues([$field => 1]));
    }
}
