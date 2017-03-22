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
use Sylius\Behat\Page\Admin\ProductVariant\CreatePageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\GeneratePageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\IndexPageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class ManagingProductVariantsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var GeneratePageInterface
     */
    private $generatePage;

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
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param GeneratePageInterface $generatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        GeneratePageInterface $generatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->generatePage = $generatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given /^I want to create a new variant of (this product)$/
     */
    public function iWantToCreateANewProduct(ProductInterface $product)
    {
        $this->createPage->open(['productId' => $product->getId()]);
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
     * @When I name it :name in :language
     */
    public function iNameItIn($name, $language)
    {
        $this->createPage->nameItIn($name, $language);
    }

    /**
     * @When I rename it to :name
     */
    public function iRenameItTo($name)
    {
        $this->updatePage->nameIt($name);
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
     * @When I disable its inventory tracking
     */
    public function iDisableItsTracking()
    {
        $this->updatePage->disableTracking();
    }

    /**
     * @When I enable its inventory tracking
     */
    public function iEnableItsTracking()
    {
        $this->updatePage->enableTracking();
    }

    /**
     * @When /^I set its(?:| default) price to "(?:€|£|\$)([^"]+)" for "([^"]+)" channel$/
     * @When I do not set its price
     */
    public function iSetItsPriceTo($price = null, $channelName = null)
    {
        $this->createPage->specifyPrice($price, (null === $channelName) ? $this->sharedStorage->get('channel') :$channelName);
    }

    /**
     * @When /^I set its original price to "(?:€|£|\$)([^"]+)" for "([^"]+)" channel$/
     */
    public function iSetItsOriginalPriceTo($originalPrice, $channelName)
    {
        $this->createPage->specifyOriginalPrice($originalPrice, $channelName);
    }

    /**
     * @When I set its height, width, depth and weight to :number
     */
    public function iSetItsDimensionsTo($value)
    {
        $this->createPage->specifyHeightWidthDepthAndWeight($value, $value, $value, $value);
    }

    /**
     * @When I do not specify its current stock
     */
    public function iDoNetSetItsCurrentStockTo()
    {
        $this->createPage->specifyCurrentStock('');
    }

    /**
     * @When I choose :calculatorName calculator
     */
    public function iChooseCalculator($calculatorName)
    {
        $this->createPage->choosePricingCalculator($calculatorName);
    }

    /**
     * @When I set its :optionName option to :optionValue
     */
    public function iSetItsOptionAs($optionName, $optionValue)
    {
        $this->createPage->selectOption($optionName, $optionValue);
    }

    /**
     * @When I set the position of :name to :position
     */
    public function iSetThePositionOfTo($name, $position)
    {
        $this->indexPage->setPosition($name, (int) $position);
    }

    /**
     * @When I save my new configuration
     */
    public function iSaveMyNewConfiguration()
    {
        $this->indexPage->savePositions();
    }

    /**
     * @When I do not want to have shipping required for this product
     */
    public function iDoNotWantToHaveShippingRequiredForThisProduct()
    {
        $this->createPage->setShippingRequired(false);
    }

    /**
     * @Then /^the (variant with code "[^"]+") should be priced at (?:€|£|\$)([^"]+) for channel "([^"]+)"$/
     */
    public function theVariantWithCodeShouldBePricedAtForChannel(ProductVariantInterface $productVariant, $price, $channelName)
    {
        $this->updatePage->open(['id' => $productVariant->getId(), 'productId' => $productVariant->getProduct()->getId()]);

        Assert::same($this->updatePage->getPriceForChannel($channelName), $price);
    }

    /**
     * @Then /^the (variant with code "[^"]+") should be named "([^"]+)" in ("([^"]+)" locale)$/
     */
    public function theVariantWithCodeShouldBeNamedIn(ProductVariantInterface $productVariant, $name, $language)
    {
        $this->updatePage->open(['id' => $productVariant->getId(), 'productId' => $productVariant->getProduct()->getId()]);

        Assert::same($this->updatePage->getNameInLanguage($language), $name);
    }

    /**
     * @Then /^the (variant with code "[^"]+") should have an original price of (?:€|£|\$)([^"]+) for channel "([^"]+)"$/
     */
    public function theVariantWithCodeShouldHaveAnOriginalPriceOfForChannel(ProductVariantInterface $productVariant, $originalPrice, $channelName)
    {
        $this->updatePage->open(['id' => $productVariant->getId(), 'productId' => $productVariant->getProduct()->getId()]);

        Assert::same(
            $this->updatePage->getOriginalPriceForChannel($channelName),
            $originalPrice
        );
    }

    /**
     * @When /^I delete the ("[^"]+" variant of product "[^"]+")$/
     * @When /^I try to delete the ("[^"]+" variant of product "[^"]+")$/
     */
    public function iDeleteTheVariantOfProduct(ProductVariantInterface $productVariant)
    {
        $this->indexPage->open(['productId' => $productVariant->getProduct()->getId()]);

        $this->indexPage->deleteResourceOnPage(['code' => $productVariant->getCode()]);
    }

    /**
     * @Then I should be notified that this variant is in use and cannot be deleted
     */
    public function iShouldBeNotifiedOfFailure()
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete, the product variant is in use.',
            NotificationType::failure()
        );
    }

    /**
     * @When /^I want to modify the ("[^"]+" product variant)$/
     */
    public function iWantToModifyAProduct(ProductVariantInterface $productVariant)
    {
        $this->updatePage->open(['id' => $productVariant->getId(), 'productId' => $productVariant->getProduct()->getId()]);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertValidationMessage($element, sprintf('Please enter the %s.', $element));
    }

    /**
     * @Then I should be notified that code has to be unique
     */
    public function iShouldBeNotifiedThatCodeHasToBeUnique()
    {
        $this->assertValidationMessage('code', 'Product variant code must be unique.');
    }

    /**
     * @Then I should be notified that current stock is required
     */
    public function iShouldBeNotifiedThatOnHandIsRequired()
    {
        $this->assertValidationMessage('on_hand', 'Please enter on hand.');
    }

    /**
     * @Then I should be notified that height, width, depth and weight cannot be lower than 0
     */
    public function iShouldBeNotifiedThatIsHeightWidthDepthWeightCannotBeLowerThan()
    {
        $this->assertValidationMessage('height', 'Height cannot be negative.');
        $this->assertValidationMessage('width', 'Width cannot be negative.');
        $this->assertValidationMessage('depth', 'Depth cannot be negative.');
        $this->assertValidationMessage('weight', 'Weight cannot be negative.');
    }

    /**
     * @Then I should be notified that price cannot be lower than 0.01
     */
    public function iShouldBeNotifiedThatPriceCannotBeLowerThen()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::contains($currentPage->getPricesValidationMessage(), 'Price must be at least 0.01.');
    }

    /**
     * @Then I should be notified that this variant already exists
     */
    public function iShouldBeNotifiedThatThisVariantAlreadyExists()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessageForForm(), 'Variant with this option set already exists.');
    }

    /**
     * @Then /^I should be notified that code is required for the (\d)(?:st|nd|rd|th) variant$/
     */
    public function iShouldBeNotifiedThatCodeIsRequiredForVariant($position)
    {
        Assert::same(
            $this->generatePage->getValidationMessage('code', $position - 1),
            'Please enter the code.'
        );
    }

    /**
     * @Then /^I should be notified that prices in all channels must be defined for the (\d)(?:st|nd|rd|th) variant$/
     */
    public function iShouldBeNotifiedThatPricesInAllChannelsMustBeDefinedForTheVariant($position)
    {
        Assert::same(
            $this->generatePage->getPricesValidationMessage($position - 1),
            'You must define price for every channel.'
        );
    }

    /**
     * @Then /^I should be notified that variant code must be unique within this product for the (\d)(?:st|nd|rd|th) variant$/
     */
    public function iShouldBeNotifiedThatVariantCodeMustBeUniqueWithinThisProductForYheVariant($position)
    {
        Assert::same(
            $this->generatePage->getValidationMessage('code', $position - 1),
            'This code must be unique within this product.'
        );
    }

    /**
     * @Then I should be notified that prices in all channels must be defined
     */
    public function iShouldBeNotifiedThatPricesInAllChannelsMustBeDefined()
    {
        Assert::contains(
            $this->createPage->getPricesValidationMessage(),
            'You must define price for every channel.'
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
     * @When I remove its name
     */
    public function iRemoveItsNameFromTranslation()
    {
        $this->updatePage->nameIt('');
    }

    /**
     * @Then /^inventory of (this variant) should not be tracked$/
     */
    public function thisProductVariantShouldNotBeTracked(ProductVariantInterface $productVariant)
    {
        $this->iWantToModifyAProduct($productVariant);

        Assert::false($this->updatePage->isTracked());
    }

    /**
     * @Then /^inventory of (this variant) should be tracked$/
     */
    public function thisProductVariantShouldBeTracked(ProductVariantInterface $productVariant)
    {
        $this->iWantToModifyAProduct($productVariant);

        Assert::true($this->updatePage->isTracked());
    }

    /**
     * @When /^I want to generate new variants for (this product)$/
     */
    public function iWantToGenerateNewVariantsForThisProduct(ProductInterface $product)
    {
        $this->generatePage->open(['productId' => $product->getId()]);
    }

    /**
     * @When I generate it
     * @When I try to generate it
     */
    public function iClickGenerate()
    {
        $this->generatePage->generate();
    }

    /**
     * @When /^I specify that the (\d)(?:st|nd|rd|th) variant is identified by "([^"]+)" code and costs "(?:€|£|\$)([^"]+)" in ("[^"]+") channel$/
     */
    public function iSpecifyThereAreVariantsIdentifiedByCodeWithCost($nthVariant, $code, $price, $channelName)
    {
        $this->generatePage->specifyCode($nthVariant - 1, $code);
        $this->generatePage->specifyPrice($nthVariant - 1, $price, $channelName);
    }

    /**
     * @When /^I specify that the (\d)(?:st|nd|rd|th) variant is identified by "([^"]+)" code$/
     */
    public function iSpecifyThereAreVariantsIdentifiedByCode($nthVariant, $code)
    {
        $this->generatePage->specifyCode($nthVariant - 1, $code);
    }

    /**
     * @When /^I specify that the (\d)(?:st|nd|rd|th) variant costs "(?:€|£|\$)([^"]+)" in ("[^"]+") channel$/
     */
    public function iSpecifyThereAreVariantsWithCost($nthVariant, $price, $channelName)
    {
        $this->generatePage->specifyPrice($nthVariant - 1, $price, $channelName);
    }

    /**
     * @When /^I remove (\d)(?:st|nd|rd|th) variant from the list$/
     */
    public function iRemoveVariantFromTheList($nthVariant)
    {
        $this->generatePage->removeVariant($nthVariant - 1);
    }

    /**
     * @Then I should be notified that it has been successfully generated
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyGenerated()
    {
        $this->notificationChecker->checkNotification('Success Product variants have been successfully generated.', NotificationType::success());
    }

    /**
     * @When I set its shipping category as :shippingCategoryName
     */
    public function iSetItsShippingCategoryAs($shippingCategoryName)
    {
        $this->createPage->selectShippingCategory($shippingCategoryName);
    }

    /**
     * @When I do not specify any information about variants
     */
    public function iDoNotSpecifyAnyInformationAboutVariants()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I change its quantity of inventory to :amount
     */
    public function iChangeItsQuantityOfInventoryTo($amount)
    {
        $this->updatePage->specifyCurrentStock($amount);
    }

    /**
     * @Then /^the (variant with code "[^"]+") should not have shipping required$/
     */
    public function theVariantWithCodeShouldNotHaveShippingRequired(ProductVariantInterface $productVariant)
    {
        $this->updatePage->open(['productId' => $productVariant->getProduct()->getId(),'id' => $productVariant->getId()]);

        Assert::false($this->updatePage->isShippingRequired());
    }

    /**
     * @Then I should be notified that on hand quantity must be greater than the number of on hold units
     */
    public function iShouldBeNotifiedThatOnHandQuantityMustBeGreaterThanTheNumberOfOnHoldUnits()
    {
        Assert::same(
            $this->updatePage->getValidationMessage('on_hand'),
            'On hand must be greater than the number of on hold units'
        );
    }

    /**
     * @param string $element
     * @param $message
     */
    private function assertValidationMessage($element, $message)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $message);
    }
}
