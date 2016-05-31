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
use Sylius\Component\Product\Model\AttributeInterface;
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
     * @Then I should see the product attribute :name in the list
     */
    public function iShouldSeeTheProductAttributeInTheList($name)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $name]),
            sprintf('The product attribute with name %s should appear on page, but it does not.', $name)
        );
    }

    /**
     * @Then the :type attribute :name should appear in the store
     */
    public function theAttributeShouldAppearInTheStore($type, $name)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceWithSpecificElementOnPage(
                ['name' => $name],
                sprintf('td span.ui.label:contains("%s")', $type)
            ),
            sprintf('The product attribute with name %s and type %s should appear on page, but it does not.', $name, $type)
        );
    }

    /**
     * @When /^I want to edit (this product attribute)$/
     */
    public function iWantToEditThisAttribute(AttributeInterface $productAttribute)
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
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code field should be disabled, but it does not.'
        );
    }

    /**
     * @Then the type field should be disabled
     */
    public function theTypeFieldShouldBeDisabled()
    {
       $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->isTypeDisabled(),
            'Type field should be disabled, but it does not.'
        );
    }

    /**
     * @Then I should be notified that product attribute with this code already exists
     */
    public function iShouldBeNotifiedThatProductAttributeWithThisCodeAlreadyExists()
    {
        Assert::true(
            $this->updatePage->checkValidationMessageFor('code', 'This code is already in use.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Given there should still be only one product attribute with code :code
     */
    public function thereShouldStillBeOnlyOneProductAttributeWithCode($code)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['code' => $code]),
            sprintf('There should be only one product attribute with code %s, but it does not.', $code)
        );
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

        Assert::false(
            $this->indexPage->isSingleResourceOnPage([$elementName => $elementValue]),
            sprintf('There should not be product attribute with %s %s, but it is.', $elementName, $elementValue)
        );
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
        Assert::same(
            $amountOfProductAttributes,
            $this->indexPage->countItems(),
            sprintf('Amount of product attributes should be equal %s, but is not.', $amountOfProductAttributes)
        );
    }

    /**
     * @When /^I delete (this product attribute)$/
     */
    public function iDeleteThisProductAttribute(AttributeInterface $productAttribute)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['code' => $productAttribute->getCode(), 'name' => $productAttribute->getName()]);
    }

    /**
     * @Then /^(this product attribute) should no longer exist in the registry$/
     */
    public function thisProductAttributeShouldNoLongerExistInTheRegistry(AttributeInterface $productAttribute)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['code' => $productAttribute->getCode()]),
            sprintf('Product attribute %s should no exist in the registry, but it does.', $productAttribute->getName())
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
            sprintf('Product attribute %s should be required.', $element)
        );
    }
}
