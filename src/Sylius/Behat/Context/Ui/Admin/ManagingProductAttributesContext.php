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
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\ProductAttribute\CreatePageInterface;
use Sylius\Behat\Page\Admin\ProductAttribute\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ManagingProductAttributesContext implements Context
{
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
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @Given I want to create a new :type product attribute
     */
    public function iWantToCreateANewTextProductAttribute($type)
    {
        $this->createPage->open(['type' => $type]);
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
    public function iSpecifyItsNameAs($name, $language)
    {
        $this->createPage->nameIt($name, $language);
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
     * @When /^I(?:| also) add material "([^"]+)"/
     */
    public function iAddMaterial($materialName)
    {
        $this->createPage->addAttributeValue($materialName);
    }

    /**
     * @Then I should see the product attribute :name in the list
     */
    public function iShouldSeeTheProductAttributeInTheList($name)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then the :type attribute :name should appear in the store
     */
    public function theAttributeShouldAppearInTheStore($type, $name)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceWithSpecificElementOnPage(
            ['name' => $name],
            sprintf('td span.ui.label:contains("%s")', $type)
        ));
    }

    /**
     * @When /^I want to edit (this product attribute)$/
     */
    public function iWantToEditThisAttribute(ProductAttributeInterface $productAttribute)
    {
        $this->updatePage->open(['id' => $productAttribute->getId()]);
    }

    /**
     * @When I change it name to :name in :language
     */
    public function iChangeItNameToIn($name, $language)
    {
        $this->updatePage->changeName($name, $language);
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
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then the type field should be disabled
     */
    public function theTypeFieldShouldBeDisabled()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true($currentPage->isTypeDisabled());
    }

    /**
     * @Then I should be notified that product attribute with this code already exists
     */
    public function iShouldBeNotifiedThatProductAttributeWithThisCodeAlreadyExists()
    {
        Assert::same($this->updatePage->getValidationMessage('code'), 'This code is already in use.');
    }

    /**
     * @Given there should still be only one product attribute with code :code
     */
    public function thereShouldStillBeOnlyOneProductAttributeWithCode($code)
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $code]));
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter attribute %s.', $element));
    }

    /**
     * @Given the attribute with :elementName :elementValue should not appear in the store
     */
    public function theAttributeWithCodeShouldNotAppearInTheStore($elementName, $elementValue)
    {
        $this->indexPage->open();

        Assert::false($this->indexPage->isSingleResourceOnPage([$elementName => $elementValue]));
    }

    /**
     * @When I remove its name from :language translation
     */
    public function iRemoveItsNameFromTranslation($language)
    {
        $this->updatePage->changeName('', $language);
    }

    /**
     * @When I want to see all product attributes in store
     */
    public function iWantToSeeAllProductAttributesInStore()
    {
        $this->indexPage->open();
    }

    /**
     * @Then /^I should see (\d+) product attributes in the list$/
     */
    public function iShouldSeeCustomersInTheList($amountOfProductAttributes)
    {
        Assert::same($this->indexPage->countItems(), (int) $amountOfProductAttributes);
    }

    /**
     * @When /^I delete (this product attribute)$/
     */
    public function iDeleteThisProductAttribute(ProductAttributeInterface $productAttribute)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['code' => $productAttribute->getCode(), 'name' => $productAttribute->getName()]);
    }

    /**
     * @Then /^(this product attribute) should no longer exist in the registry$/
     */
    public function thisProductAttributeShouldNoLongerExistInTheRegistry(ProductAttributeInterface $productAttribute)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['code' => $productAttribute->getCode()]));
    }

    /**
     * @Then the first product attribute on the list should have name :name
     */
    public function theFirstProductAttributeOnTheListShouldHave($name)
    {
        $names = $this->indexPage->getColumnFields('name');

        Assert::same(reset($names), $name);
    }

    /**
     * @Then the last product attribute on the list should have name :name
     */
    public function theLastProductAttributeOnTheListShouldHave($name)
    {
        $names = $this->indexPage->getColumnFields('name');

        Assert::same(end($names), $name);
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
}
