<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Promotion\IndexPageInterface;
use Sylius\Behat\Page\Admin\Promotion\CreatePageInterface;
use Sylius\Behat\Page\Admin\Promotion\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ManagingPromotionsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new promotion
     */
    public function iWantToCreateANewPromotion()
    {
        $this->createPage->open();
    }

    /**
     * @Given I want to browse promotions
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
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I name it :name
     * @When I do not name it
     * @When I remove its name
     */
    public function iNameIt($name = null)
    {
        $this->createPage->nameIt($name);
    }

    /**
     * @Then the :promotionName promotion should appear in the registry
     * @Then the :promotionName promotion should exist in the registry
     * @Then this promotion should still be named :promotionName
     * @Then promotion :promotionName should still exist in the registry
     */
    public function thePromotionShouldAppearInTheRegistry($promotionName)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $promotionName]),
            sprintf('Promotion with name %s has not been found.', $promotionName)
        );
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
     * @Given I add the "Contains taxon" rule configured with :count :taxonName
     */
    public function iAddTheContainsTaxonRuleConfiguredWith($count, $taxonName)
    {
        $this->createPage->addRule('Contains taxon');
        $this->createPage->selectRuleOption('Taxon', $taxonName);
        $this->createPage->fillRuleOption('Count', $count);
    }

    /**
     * @Given I add the "Taxon" rule configured with :firstTaxon
     * @Given I add the "Taxon" rule configured with :firstTaxon and :secondTaxon
     */
    public function iAddTheTaxonRuleConfiguredWith($firstTaxon, $secondTaxon = null)
    {
        $this->createPage->addRule('Taxon');
        $this->createPage->selectRuleOption('Taxons', $firstTaxon, true);

        if (null !== $secondTaxon) {
            $this->createPage->selectRuleOption('Taxons', $secondTaxon, true);
        }
    }

    /**
     * @Given I add the "Total of items from taxon" rule configured with :count :taxonName
     */
    public function iAddTheRuleConfiguredWith($count, $taxonName)
    {
        $this->createPage->addRule('Total of items from taxon');
        $this->createPage->selectRuleOption('Taxon', $taxonName);
        $this->createPage->fillRuleOption('Amount', $count);
    }

    /**
     * @Given I add the "Order fixed discount" action configured with €:amount
     */
    public function stepDefinition($amount)
    {
        $this->createPage->addAction('Order fixed discount');
        $this->createPage->fillActionOption('Amount', $amount);
    }

    /**
     * @Then /^there should be (\d+) promotion(?:|s)$/
     */
    public function thereShouldBePromotion($number)
    {
        Assert::eq(
            $number,
            $this->indexPage->countItems(),
            'I should see %s promotions but i see only %2$s'
        );
    }

    /**
     * @Then /^(this promotion) should be coupon based$/
     */
    public function thisPromotionShouldBeCouponBased(PromotionInterface $promotion)
    {
        Assert::true(
            $this->indexPage->isCouponBasedFor($promotion),
            sprintf('Promotion with name "%s" should be coupon based', $promotion->getName())
        );
    }

    /**
     * @Then /^I should be able to manage coupons for (this promotion)$/
     */
    public function iShouldBeAbleToManageCouponsForThisPromotion(PromotionInterface $promotion)
    {
        Assert::true(
            $this->indexPage->isAbleToManageCouponsFor($promotion),
            sprintf('I should be able to manage coupons for given promotion with name %s but apparently i am not.', $promotion->getName())
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter promotion %s.', $element));
    }

    /**
     * @Then I should be notified that promotion with this code already exists
     */
    public function iShouldBeNotifiedThatPromotionWithThisCodeAlreadyExists()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('code', 'The promotion with given code already exists.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then promotion with :element :name should not be added
     */
    public function promotionWithElementValueShouldNotBeAdded($element, $name)
    {
        $this->indexPage->open();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage([$element => $name]),
            sprintf('Promotion with %s "%s" has been created, but it should not.', $element, $name)
        );
    }

    /**
     * @Then there should still be only one promotion with :element :value
     */
    public function thereShouldStillBeOnlyOnePromotionWith($element, $value)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage([$element => $value]),
            sprintf('Promotion with %s "%s" cannot be found.', $element, $value)
        );
    }

    /**
     * @When I set its usage limit to :usageLimit
     */
    public function iSetItsUsageLimitTo($usageLimit)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->fillUsageLimit($usageLimit);
    }

    /**
     * @Then the :promotion promotion should be available to be used only :usageLimit times
     */
    public function thePromotionShouldBeAvailableToUseOnlyTimes(PromotionInterface $promotion, $usageLimit)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true(
            $this->updatePage->hasResourceValues(['usage_limit' => $usageLimit]),
            sprintf('Promotion %s does not have usage limit set to %s.', $promotion->getName(), $usageLimit)
        );
    }

    /**
     * @When I make it exclusive
     */
    public function iMakeItExclusive()
    {
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
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->checkChannel($channelName);
    }

    /**
     * @Then the :promotion promotion should be applicable for the :channelName channel
     */
    public function thePromotionShouldBeApplicableForTheChannel(PromotionInterface $promotion, $channelName)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true(
            $this->updatePage->checkChannelsState($channelName),
            sprintf('Promotion %s is not %s, but it should be.', $promotion->getName(), $channelName)
        );
    }

    /**
     * @Given I want to modify a :promotion promotion
     * @Given /^I want to modify (this promotion)$/
     */
    public function iWantToModifyAPromotion(PromotionInterface $promotion)
    {
        $this->updatePage->open(['id' => $promotion->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code should be immutable, but it does not.'
        );
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

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['code' => $promotion->getCode()]),
            sprintf('Promotion with code %s exists but should not.', $promotion->getCode())
        );
    }

    /**
     * @Then I should be notified that it is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            "Cannot delete, the promotion is in use.",
            NotificationType::failure()
        );
    }

    /**
     * @When I make it available from :startsDate to :endsDate
     */
    public function iMakeItAvailableFromTo(\DateTime $startsDate, \DateTime $endsDate)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setStartsAt($startsDate);
        $currentPage->setEndsAt($endsDate);
    }

    /**
     * @Then the :promotion promotion should be available from :startsDate to :endsDate
     */
    public function thePromotionShouldBeAvailableFromTo(PromotionInterface $promotion, \DateTime $startsDate, \DateTime $endsDate)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true(
            $this->updatePage->hasStartsAt($startsDate),
            sprintf('Promotion %s should starts at %s, but it isn\'t.', $promotion->getName(), date('D, d M Y H:i:s', $startsDate->getTimestamp()))
        );

        Assert::true(
            $this->updatePage->hasEndsAt($endsDate),
            sprintf('Promotion %s should ends at %s, but it isn\'t.', $promotion->getName(), date('D, d M Y H:i:s', $endsDate->getTimestamp()))
        );
    }

    /**
     * @Then I should be notified that promotion cannot end before it start
     */
    public function iShouldBeNotifiedThatPromotionCannotEndBeforeItsEvenStart()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor('ends_at', 'End date cannot be set prior start date.'),
            'Start date was set after ends date, but it should not be possible.'
        );
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('Promotion %s should be required.', $element)
        );
    }

    /**
     * @param PromotionInterface $promotion
     * @param string $field
     */
    private function assertIfFieldIsTrue(PromotionInterface $promotion, $field)
    {
        $this->iWantToModifyAPromotion($promotion);

        Assert::true(
            $this->updatePage->hasResourceValues([$field => 1]),
            sprintf('Promotion %s is not %s, but it should be.', $promotion->getName(), str_replace('_', ' ', $field))
        );
    }
}
