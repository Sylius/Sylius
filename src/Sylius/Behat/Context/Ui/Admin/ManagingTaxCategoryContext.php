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
use Sylius\Behat\Service\Accessor\NotificationAccessorInterface;
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
     * @var NotificationAccessorInterface
     */
    private $notificationAccessor;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param IndexPageInterface $taxCategoryIndexPage
     * @param NotificationAccessorInterface $notificationAccessor
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        IndexPageInterface $taxCategoryIndexPage,
        NotificationAccessorInterface $notificationAccessor
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->taxCategoryIndexPage = $taxCategoryIndexPage;
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
}
