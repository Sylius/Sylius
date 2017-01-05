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
use Sylius\Component\Product\Resolver\DefaultProductVariantResolver;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingProductVariantsContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var DefaultProductVariantResolver
     */
    private $defaultProductVariantResolver;

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
     * @param DefaultProductVariantResolver $defaultProductVariantResolver
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param GeneratePageInterface $generatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultProductVariantResolver $defaultProductVariantResolver,
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        GeneratePageInterface $generatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->defaultProductVariantResolver = $defaultProductVariantResolver;
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
    public function iSetItsPriceTo($price = null, $channel = null)
    {
        $this->createPage->specifyPrice($price, (null === $channel) ? $this->sharedStorage->get('channel') :$channel);
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
     * @When I start sorting variants by :field
     */
    public function iSortProductsBy($field)
    {
        $this->indexPage->sortBy($field);
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
     * @Then the :productVariantCode variant of the :product product should appear in the store
     */
    public function theProductVariantShouldAppearInTheShop($productVariantCode, ProductInterface $product)
    {
        $this->iWantToViewAllVariantsOfThisProduct($product);

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $productVariantCode]),
            sprintf('The product variant with code %s has not been found.', $productVariantCode)
        );
    }

    /**
     * @Then the :productVariantCode variant of the :product product should not appear in the store
     */
    public function theProductVariantShouldNotAppearInTheShop($productVariantCode, ProductInterface $product)
    {
        $this->iWantToViewAllVariantsOfThisProduct($product);

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['code' => $productVariantCode]),
            sprintf('The product variant with code %s has not been found.', $productVariantCode)
        );
    }

    /**
     * @Then the :product product should have no variants
     */
    public function theProductShouldHaveNoVariants(ProductInterface $product)
    {
        $this->assertNumberOfVariantsOnProductPage($product, 0);
    }

    /**
     * @Then the :product product should have only one variant
     */
    public function theProductShouldHaveOnlyOneVariant(ProductInterface $product)
    {
        $this->assertNumberOfVariantsOnProductPage($product, 1);
    }

    /**
     * @Then /^the (variant with code "[^"]+") should be priced at (?:€|£|\$)([^"]+) for channel "([^"]+)"$/
     */
    public function theVariantWithCodeShouldBePricedAtForChannel(ProductVariantInterface $productVariant, $price, $channelName)
    {
        $this->updatePage->open(['id' => $productVariant->getId(), 'productId' => $productVariant->getProduct()->getId()]);

        Assert::same(
            $this->updatePage->getPriceForChannel($channelName),
            $price
        );
    }

    /**
     * @Then /^the (variant with code "[^"]+") should be named "([^"]+)" in ("([^"]+)" locale)$/
     */
    public function theVariantWithCodeShouldBeNamedIn(ProductVariantInterface $productVariant, $name, $language)
    {
        $this->updatePage->open(['id' => $productVariant->getId(), 'productId' => $productVariant->getProduct()->getId()]);

        Assert::same($name, $this->updatePage->getNameInLanguage($language));
    }

    /**
     * @When /^I (?:|want to )view all variants of (this product)$/
     * @When /^I view(?:| all) variants of the (product "[^"]+")$/
     */
    public function iWantToViewAllVariantsOfThisProduct(ProductInterface $product)
    {
        $this->indexPage->open(['productId' => $product->getId()]);
    }

    /**
     * @Then I should see :numberOfProductVariants variants in the list
     * @Then I should see :numberOfProductVariants variant in the list
     * @Then I should not see any variants in the list
     */
    public function iShouldSeeProductVariantsInTheList($numberOfProductVariants = 0)
    {
        $foundRows = $this->indexPage->countItems();

        Assert::same(
            (int) $numberOfProductVariants,
            $foundRows,
            '%s rows with product variants should appear on page, %s rows has been found'
        );
    }

    /**
     * @When /^I delete the ("[^"]+" variant of product "[^"]+")$/
     * @When /^I try to delete the ("[^"]+" variant of product "[^"]+")$/
     */
    public function iDeleteTheVariantOfProduct(ProductVariantInterface $productVariant)
    {
        $this->iWantToViewAllVariantsOfThisProduct($productVariant->getProduct());

        $this->indexPage->deleteResourceOnPage(['code' => $productVariant->getCode()]);
    }

    /**
     * @Then /^(this variant) should not exist in the product catalog$/
     */
    public function productVariantShouldNotExist(ProductVariantInterface $productVariant)
    {
        $this->iWantToViewAllVariantsOfThisProduct($productVariant->getProduct());

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['name' => $productVariant->getName()]),
            sprintf('Product variant with code %s exists but should not.', $productVariant->getName())
        );
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
     * @Then /^(this variant) should still exist in the product catalog$/
     */
    public function productShouldExistInTheProductCatalog(ProductVariantInterface $productVariant)
    {
        $this->theProductVariantShouldAppearInTheShop($productVariant->getCode(), $productVariant->getProduct());
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
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code should be immutable, but it does not.'
        );
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

        Assert::same($currentPage->getFirstPriceValidationMessage(), 'Price must be at least 0.01.');
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
        Assert::same(
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
     * @Then /^the variant "([^"]+)" should have (\d+) items on hand$/
     */
    public function thisVariantShouldHaveItemsOnHand($productVariantName, $quantity)
    {
        Assert::true(
            $this->indexPage->isSingleResourceWithSpecificElementOnPage(['name' => $productVariantName], sprintf('td > div.ui.label:contains("%s")', $quantity)),
            sprintf('The product variant %s should have %s items on hand, but it does not.', $productVariantName, $quantity)
        );
    }

    /**
     * @Then /^the "([^"]+)" variant of ("[^"]+" product) should have (\d+) items on hand$/
     */
    public function theVariantOfProductShouldHaveItemsOnHand($productVariantName, ProductInterface $product, $quantity)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        Assert::true(
            $this->indexPage->isSingleResourceWithSpecificElementOnPage(['name' => $productVariantName], sprintf('td > div.ui.label:contains("%s")', $quantity)),
            sprintf('The product variant %s should have %s items on hand, but it does not.', $productVariantName, $quantity)
        );
    }

    /**
     * @Then /^inventory of (this variant) should not be tracked$/
     */
    public function thisProductVariantShouldNotBeTracked(ProductVariantInterface $productVariant)
    {
        $this->iWantToModifyAProduct($productVariant);

        Assert::false(
            $this->updatePage->isTracked(),
            'This variant should not be tracked, but it is.'
        );
    }

    /**
     * @Then /^inventory of (this variant) should be tracked$/
     */
    public function thisProductVariantShouldBeTracked(ProductVariantInterface $productVariant)
    {
        $this->iWantToModifyAProduct($productVariant);

        Assert::true(
            $this->updatePage->isTracked(),
            'This variant should be tracked, but it is not.'
        );
    }

    /**
     * @Then /^I should see that the ("([^"]+)" variant) is not tracked$/
     */
    public function iShouldSeeThatIsNotTracked(ProductVariantInterface $productVariant)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $productVariant->getName(), 'inventory' => 'Not tracked']),
            sprintf('This "%s" variant should have label not tracked, but it does not have', $productVariant->getName())
        );
    }

    /**
     * @Then /^I should see that the ("[^"]+" variant) has zero on hand quantity$/
     */
    public function iShouldSeeThatTheVariantHasZeroOnHandQuantity(ProductVariantInterface $productVariant)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $productVariant->getName(), 'inventory' => '0 Available on hand']),
            sprintf('This "%s" variant should have 0 on hand quantity, but it does not.', $productVariant->getName())
        );
    }

    /**
     * @Then /^(\d+) units of (this product) should be on hold$/
     */
    public function unitsOfThisProductShouldBeOnHold($quantity, ProductInterface $product)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->defaultProductVariantResolver->getVariant($product);

        $this->assertOnHoldQuantityOfVariant($quantity, $variant);
    }

    /**
     * @Then /^(\d+) units of (this product) should be on hand$/
     */
    public function unitsOfThisProductShouldBeOnHand($quantity, ProductInterface $product)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->defaultProductVariantResolver->getVariant($product);
        $actualQuantity = $this->indexPage->getOnHandQuantityFor($variant);

        Assert::same(
            (int) $quantity,
            $actualQuantity,
            sprintf(
                'Unexpected on hand quantity for "%s" variant. It should be "%s" but is "%s"',
                $variant->getName(),
                $quantity,
                $actualQuantity
            )
        );
    }

    /**
     * @Then /^there should be no units of (this product) on hold$/
     */
    public function thereShouldBeNoUnitsOfThisProductOnHold(ProductInterface $product)
    {
        /** @var ProductVariantInterface $variant */
        $variant = $this->defaultProductVariantResolver->getVariant($product);

        $this->assertOnHoldQuantityOfVariant(0, $variant);
    }

    /**
     * @Then the :variant variant should have :amount items on hold
     */
    public function thisVariantShouldHaveItemsOnHold(ProductVariantInterface $variant, $amount)
    {
        $this->assertOnHoldQuantityOfVariant((int) $amount, $variant);
    }

    /**
     * @Then the :variant variant of :product product should have :amount items on hold
     */
    public function theVariantOfProductShouldHaveItemsOnHold(ProductVariantInterface $variant, ProductInterface $product, $amount)
    {
        $this->indexPage->open(['productId' => $product->getId()]);

        $this->assertOnHoldQuantityOfVariant((int) $amount, $variant);
    }

    /**
     * @Then the first variant in the list should have :field :value
     */
    public function theFirstVariantInTheListShouldHave($field, $value)
    {
        $actualValue = $this->indexPage->getColumnFields($field)[0];

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected first variant\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
    }

    /**
     * @Then the last variant in the list should have :field :value
     */
    public function theLastVariantInTheListShouldHave($field, $value)
    {
        $fields = $this->indexPage->getColumnFields($field);
        $actualValue = end($fields);

        Assert::same(
            $actualValue,
            $value,
            sprintf('Expected last variant\'s %s to be "%s", but it is "%s".', $field, $value, $actualValue)
        );
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
     * @Then /^(this variant) should have a (\d+) item currently in stock$/
     */
    public function thisVariantShouldHaveAItemCurrentlyInStock(ProductVariantInterface $productVariant, $amountInStock)
    {
        $this->indexPage->open(['productId' => $productVariant->getProduct()->getId()]);

        Assert::same(
            $this->indexPage->getOnHandQuantityFor($productVariant),
            (int) $amountInStock
        );
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

    /**
     * @param int $expectedAmount
     * @param ProductVariantInterface $variant
     *
     * @throws \InvalidArgumentException
     */
    private function assertOnHoldQuantityOfVariant($expectedAmount, $variant)
    {
        $actualAmount = $this->indexPage->getOnHoldQuantityFor($variant);

        Assert::same(
            (int) $expectedAmount,
            $actualAmount,
            sprintf(
                'Unexpected on hold quantity for "%s" variant. It should be "%s" but is "%s"',
                $variant->getName(),
                $expectedAmount,
                $actualAmount
            )
        );
    }

    /**
     * @param ProductInterface $product
     * @param int $amount
     */
    private function assertNumberOfVariantsOnProductPage(ProductInterface $product, $amount)
    {
        $this->iWantToViewAllVariantsOfThisProduct($product);

        Assert::same((int) $this->indexPage->countItems(), $amount, 'Product has %d variants, but should have %d');
    }
}
