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
use Sylius\Behat\Page\Admin\ProductOption\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Service\CurrentPageResolverInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ManagingProductOptionsContext implements Context
{
    const RESOURCE_NAME = 'product_option';

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
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new product option
     */
    public function iWantToCreateANewProductOption()
    {
        $this->createPage->open();
    }

    /**
     * @When I browse product options
     */
    public function iBrowseProductOptions()
    {
        $this->indexPage->open();
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
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I name it :name in :language
     */
    public function iNameItInLanguage($name, $language)
    {
        $this->createPage->nameItIn($name, $language);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs($code)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I add the option value with code :code and value :value
     */
    public function iAddTheOptionValueWithCodeAndValue($code, $value)
    {
        $this->createPage->addOptionValue($code, $value);
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfullyCreated()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then the product option :productOptionName should appear in the registry
     */
    public function theProductOptionShouldAppearInTheRegistry($productOptionName)
    {
        $this->iBrowseProductOptions();

        Assert::true(
            $this->indexPage->isResourceOnPage(['name' => $productOptionName]),
            sprintf('The shipping method with name %s has not been found.', $productOptionName)
        );
    }
}
