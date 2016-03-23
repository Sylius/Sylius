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
use Sylius\Behat\Page\Admin\TaxCategory\UpdatePageInterface;
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
use Sylius\Behat\Page\Admin\TaxCategory\CreatePageInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingTaxCategoryContext implements Context
{
    const RESOURCE_NAME = 'tax_category';

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var IndexPageInterface
     */
    private $taxCategoryIndexPage;

    /**
     * @var CreatePageInterface
     */
    private $taxCategoryCreatePage;

    /**
     * @var UpdatePageInterface
     */
    private $taxCategoryUpdatePage;

    /**
     * @var NotificationAccessorInterface
     */
    private $notificationAccessor;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $taxCategoryIndexPage
     * @param CreatePageInterface $taxCategoryCreatePage
     * @param UpdatePageInterface $taxCategoryUpdatePage
     * @param NotificationAccessorInterface $notificationAccessor
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $taxCategoryIndexPage,
        CreatePageInterface $taxCategoryCreatePage,
        UpdatePageInterface $taxCategoryUpdatePage,
        NotificationAccessorInterface $notificationAccessor
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->taxCategoryIndexPage = $taxCategoryIndexPage;
        $this->taxCategoryCreatePage = $taxCategoryCreatePage;
        $this->taxCategoryUpdatePage = $taxCategoryUpdatePage;
        $this->notificationAccessor = $notificationAccessor;
    }

    /**
     * @When I delete tax category :taxCategory
     */
    public function iDeletedTaxCategory(TaxCategoryInterface $taxCategory)
    {
        $this->taxCategoryIndexPage->open();
        $this->taxCategoryIndexPage->deleteResourceOnPage(['code' => $taxCategory->getCode()]);
        $this->sharedStorage->set('tax_category', $taxCategory);
    }

    /**
     * @Then /^(this tax category) should no longer exist in the registry$/
     */
    public function thisTaxCategoryShouldNoLongerExistInTheRegistry(TaxCategoryInterface $taxCategory)
    {
        Assert::false(
            $this->taxCategoryIndexPage->isResourceOnPage(['code' => $taxCategory->getCode()]),
            sprintf('Tax category with code %s exists but should not', $taxCategory->getCode())
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedAboutSuccessfulDeletion()
    {
        Assert::true(
            $this->notificationAccessor->hasSuccessMessage(),
            'Message type is not positive'
        );

        Assert::true(
            $this->notificationAccessor->isSuccessfullyDeletedFor(self::RESOURCE_NAME),
            'Successful deletion message does not appear'
        );
    }

    /**
     * @Given I want to create a new tax category
     */
    public function iWantToCreateNewTaxCategory()
    {
        $this->taxCategoryCreatePage->open();
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs($code)
    {
        $this->taxCategoryCreatePage->specifyCode($code);
    }

    /**
     * @When I name it :name
     */
    public function iNameIt($name)
    {
        $this->taxCategoryCreatePage->nameIt($name);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->taxCategoryCreatePage->create();
    }

    /**
     * @Then the tax category :taxCategory should appear in the registry
     */
    public function thisTaxCategoryShouldAppearInTheRegistry(TaxCategoryInterface $taxCategory)
    {
        $this->taxCategoryUpdatePage->isOpen();
        Assert::true(
            $this->taxCategoryUpdatePage->hasResourceValues(
                [
                    'name' => $taxCategory->getName(),
                    'code' => $taxCategory->getCode(),
                    'description' => $taxCategory->getDescription(),
                ]
            ),
            sprintf('Tax category with code %s was found, but fields are not assigned properly', $taxCategory->getCode())
        );
    }

    /**
     * @When I describe it as :describes
     */
    public function iDescribeItAs($describes)
    {
        $this->taxCategoryCreatePage->describeItAs($describes);
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedAboutSuccessfulCreation()
    {
        Assert::true(
            $this->notificationAccessor->hasSuccessMessage(),
            'Message type is not positive'
        );

        Assert::true(
            $this->notificationAccessor->isSuccessfullyCreatedFor(self::RESOURCE_NAME),
            'Successful creation message does not appear'
        );
    }

    /**
     * @Given I want to modify tax category :taxCategory
     */
    public function iWantToModifyNewTaxCategory(TaxCategoryInterface $taxCategory)
    {
        $this->taxCategoryUpdatePage->open(['id' => $taxCategory->getId()]);
    }

    /**
     * @When I save my changes
     */
    public function iSaveMyChanges()
    {
        $this->taxCategoryUpdatePage->saveChanges();
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->taxCategoryUpdatePage->isCodeDisabled(),
            'Code should be immutable, but it does not'
        );
    }
}
