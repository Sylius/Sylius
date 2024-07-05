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
use Sylius\Behat\Element\Admin\Locale\FormElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingLocalesContext implements Context
{
    public function __construct(
        private CreatePageInterface $createPage,
        private IndexPageInterface $indexPage,
        private FormElementInterface $formElement,
        private NotificationCheckerInterface $notificationChecker,
    ) {
    }

    /**
     * @When I want to create a new locale
     * @When I want to add a new locale
     */
    public function iWantToCreateNewLocale(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I choose :name
     */
    public function iChoose(string $name): void
    {
        $this->formElement->chooseLocale($name);
    }

    /**
     * @When I add it
     */
    public function iAdd(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I remove :localeCode locale
     */
    public function iRemoveLocale(string $localeCode): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['code' => $localeCode]);
    }

    /**
     * @Then the store should be available in the :name language
     */
    public function storeShouldBeAvailableInLanguage(string $name): void
    {
        $doesLocaleExist = $this->indexPage->isSingleResourceOnPage(['name' => $name]);

        Assert::true($doesLocaleExist);
    }

    /**
     * @Then I should not be able to choose :name
     */
    public function iShouldNotBeAbleToChoose(string $name): void
    {
        Assert::false($this->formElement->isLocaleAvailable($name));
    }

    /**
     * @Then I should be informed that locale :localeCode has been deleted
     */
    public function iShouldBeInformedThatLocaleHasBeenDeleted(string $localeCode): void
    {
        $this->notificationChecker->checkNotification(
            'Locale has been successfully deleted.',
            NotificationType::success(),
        );
    }

    /**
     * @Then only the :localeCode locale should be present in the system
     */
    public function onlyTheLocaleShouldBePresentInTheSystem(string $localeCode): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $localeCode]));
        Assert::true($this->indexPage->countItems() === 1);
    }

    /**
     * @Then I should be informed that locale :localeCode is in use and cannot be deleted
     */
    public function iShouldBeInformedThatLocaleIsInUseAndCannotBeDeleted(string $localeCode): void
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete the locale, as it is used by at least one translation.',
            NotificationType::failure(),
        );
    }

    /**
     * @Then the :localeCode locale should be still present in the system
     */
    public function theLocaleShouldBeStillPresentInTheSystem(string $localeCode): void
    {
        Assert::true($this->indexPage->isSingleResourceOnPage(['code' => $localeCode]));
    }
}
