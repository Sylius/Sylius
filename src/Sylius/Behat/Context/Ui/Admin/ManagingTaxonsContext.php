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
use Sylius\Behat\Element\Admin\Taxon\FormElementInterface;
use Sylius\Behat\Element\Admin\Taxon\ImageFormElementInterface;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Behat\Page\Admin\Taxon\CreatePageInterface;
use Sylius\Behat\Service\Helper\JavaScriptTestHelper;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ManagingTaxonsContext implements Context
{
    public function __construct(
        private readonly SharedStorageInterface $sharedStorage,
        private readonly CreatePageInterface $createPage,
        private readonly CreatePageInterface $createForParentPage,
        private readonly UpdatePageInterface $updatePage,
        private readonly FormElementInterface $formElement,
        private readonly ImageFormElementInterface $imageFormElement,
        private readonly NotificationCheckerInterface $notificationChecker,
        private readonly JavaScriptTestHelper $testHelper,
        private readonly UpdateSimpleProductPageInterface $updateSimpleProductPage,
    ) {
    }

    /**
     * @When I want to create a new taxon
     * @When I want to see all taxons in store
     */
    public function iWantToCreateANewTaxon(): void
    {
        $this->createPage->open();
    }

    /**
     * @When I want to create a new taxon for :taxon
     */
    public function iWantToCreateANewTaxonForParent(TaxonInterface $taxon): void
    {
        $this->testHelper->waitUntilPageOpens($this->createForParentPage, ['id' => $taxon->getId()]);
    }

    /**
     * @When /^I want to modify the ("[^"]+" taxon)$/
     */
    public function iWantToModifyATaxon(TaxonInterface $taxon): void
    {
        $this->sharedStorage->set('taxon', $taxon);

        $this->testHelper->waitUntilPageOpens($this->updatePage, ['id' => $taxon->getId()]);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->formElement->specifyCode($code ?? '');
    }

    /**
     * @When I specify a too long code
     */
    public function iSpecifyATooLong(): void
    {
        $this->formElement->specifyCode(str_repeat('a', 256));
    }

    /**
     * @When I name it :name in :localeCode
     * @When I rename it to :name in :localeCode
     * @When I do not specify its name
     */
    public function iNameItIn(?string $name = null, ?string $localeCode = 'en_US'): void
    {
        $this->formElement->nameIt($name ?? '', $localeCode);
    }

    /**
     * @When I set its slug to :slug
     * @When I do not specify its slug
     * @When I set its slug to :slug in :localeCode
     */
    public function iSetItsSlugToIn(?string $slug = null, ?string $localeCode = 'en_US'): void
    {
        $this->formElement->slugIt($slug ?? '', $localeCode);
    }

    /**
     * @When I generate its slug in :localeCode
     */
    public function iGenerateItsSlugIn(string $localeCode): void
    {
        $this->formElement->generateSlug($localeCode);
    }

    /**
     * @When I change its description to :description in :localeCode
     */
    public function iChangeItsDescriptionToIn(string $description, string $localeCode): void
    {
        $this->formElement->describeItAs($description, $localeCode);
    }

    /**
     * @When I describe it as :description in :localeCode
     */
    public function iDescribeItAs(string $description, string $localeCode): void
    {
        $this->formElement->describeItAs($description, $localeCode);
    }

    /**
     * @Given /^I set its (parent taxon to "[^"]+")$/
     * @Given /^I change its (parent taxon to "[^"]+")$/
     * @Then /^I should be able to change its (parent taxon to "[^"]+")$/
     */
    public function iChangeItsParentTaxonTo(TaxonInterface $taxon): void
    {
        $this->formElement->chooseParent($taxon);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     * @When I save my changes to the images
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I attach the :path image with :type type
     * @When I attach the :path image with :type type to this taxon
     * @When I attach the :path image
     * @When I attach the :path image to this taxon
     */
    public function iAttachImageWithType(string $path, ?string $type = null): void
    {
        $this->imageFormElement->attachImage($path, $type);
    }

    /**
     * @When /^I(?:| also) remove an image with "([^"]*)" type$/
     */
    public function iRemoveAnImageWithType(string $type): void
    {
        $this->imageFormElement->removeImageWithType($type);
    }

    /**
     * @When I remove the first image
     */
    public function iRemoveTheFirstImage(): void
    {
        $this->imageFormElement->removeFirstImage();
    }

    /**
     * @When I move up :taxonName taxon
     */
    public function iMoveUpTaxon(string $taxonName): void
    {
        $this->createPage->moveUpTaxon($taxonName);
    }

    /**
     * @When I move down :taxonName taxon
     */
    public function iMoveDownTaxon(string $taxonName): void
    {
        $this->createPage->moveDownTaxon($taxonName);
    }

    /**
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->formElement->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->formElement->disable();
    }

    /**
     * @Then /^the ("[^"]+" taxon) should appear in the registry$/
     */
    public function theTaxonShouldAppearInTheRegistry(TaxonInterface $taxon): void
    {
        $this->updatePage->open(['id' => $taxon->getId()]);
        Assert::same($this->formElement->getCode(), $taxon->getCode());
    }

    /**
     * @Then this taxon :element should be :value
     * @Then this taxon :element should be :value in :localeCode
     * @Then this taxon should have :element :value in :localeCode
     */
    public function thisTaxonElementShouldBe(string $element, string $value, ?string $localeCode = 'en_US'): void
    {
        Assert::same($this->formElement->getTranslationFieldValue($element, $localeCode), $value);
    }

    /**
     * @Then the slug of the :taxonName taxon should( still) be :slug
     */
    public function theSlugOfTheTaxonShouldBe(string $taxonName, string $slug): void
    {
        $this->thisTaxonElementShouldBe('slug', $slug);
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->formElement->isCodeDisabled());
    }

    /**
     * @Then the product :product should no longer have a main taxon
     */
    public function theProductShouldNoLongerHaveAMainTaxon(ProductInterface $product): void
    {
        $this->updateSimpleProductPage->open(['id' => $product->getId()]);

        Assert::false($this->updateSimpleProductPage->hasMainTaxon());
    }

    /**
     * @Then /^this taxon should (belongs to "[^"]+")$/
     */
    public function thisTaxonShouldBelongsTo(TaxonInterface $taxon): void
    {
        Assert::same($this->formElement->getParent(), $taxon->getCode());
    }

    /**
     * @Then it should not belong to any other taxon
     */
    public function itShouldNotBelongToAnyOtherTaxon(): void
    {
        Assert::isEmpty($this->formElement->getParent());
    }

    /**
     * @Then I should be notified that taxon with this code already exists
     */
    public function iShouldBeNotifiedThatTaxonWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->formElement->getValidationMessage('code'), 'Taxon with given code already exists.');
    }

    /**
     * @Then I should be notified that taxon slug must be unique
     */
    public function iShouldBeNotifiedThatTaxonSlugMustBeUnique(): void
    {
        Assert::same(
            $this->formElement->getValidationMessage('slug', ['%locale_code%' => 'en_US']),
            'Taxon slug must be unique.',
        );
    }

    /**
     * @Then /^I should be notified that (name|slug) is required$/
     */
    public function iShouldBeNotifiedThatTranslationFieldIsRequired(string $element): void
    {
        Assert::same(
            $this->formElement->getValidationMessage($element, ['%locale_code%' => 'en_US']),
            sprintf('Please enter taxon %s.', $element),
        );
    }

    /**
     * @Then I should be notified that code is required
     */
    public function iShouldBeNotifiedThatCodeIsRequired(): void
    {
        Assert::same(
            $this->formElement->getValidationMessage('code', ['%locale_code%' => 'en_US']),
            'Please enter taxon code.',
        );
    }

    /**
     * @Then I should be notified that code is too long
     */
    public function iShouldBeNotifiedThatCodeIsTooLong(): void
    {
        Assert::contains(
            $this->formElement->getValidationMessage('code'),
            'must not be longer than 255 characters.',
        );
    }

    /**
     * @Then /^there should(?:| still) be only one taxon with code "([^"]+)"$/
     */
    public function thereShouldStillBeOnlyOneTaxonWithCode(string $code): void
    {
        Assert::same($this->formElement->getCode(), $code);
    }

    /**
     * @Then /^taxon named "([^"]+)" should not be added$/
     * @Then the taxon named :name should no longer exist in the registry
     */
    public function taxonNamedShouldNotBeAdded(string $name): void
    {
        if (!$this->createPage->isOpen()) {
            $this->createPage->open();
        }

        Assert::same($this->createPage->countTaxonsByName($name), 0);
    }

    /**
     * @Then /^I should see (\d+) taxons on the list$/
     */
    public function iShouldSeeTaxonsInTheList(int $number): void
    {
        Assert::same($this->createPage->countTaxons(), (int) $number);
    }

    /**
     * @Then I should see the taxon named :name in the list
     */
    public function iShouldSeeTheTaxonNamedInTheList(string $name): void
    {
        Assert::same($this->createPage->countTaxonsByName($name), 1);
    }

    /**
     * @Then /^(?:it|this taxon) should(?:| also) have an image with "([^"]*)" type$/
     */
    public function thisTaxonShouldHaveAnImageWithType(string $type): void
    {
        Assert::true($this->imageFormElement->isImageWithTypeDisplayed($type));
    }

    /**
     * @Then /^(?:this taxon|it) should not have(?:| also) any images with "([^"]*)" type$/
     */
    public function thisTaxonShouldNotHaveAnImageWithType(string $code): void
    {
        Assert::false($this->imageFormElement->isImageWithTypeDisplayed($code));
    }

    /**
     * @Then /^(this taxon) should not have any images$/
     */
    public function thisTaxonShouldNotHaveAnyImages(TaxonInterface $taxon): void
    {
        $this->iWantToModifyATaxon($taxon);

        Assert::same($this->imageFormElement->countImages(), 0);
    }

    /**
     * @When I change the image with the :type type to :path
     */
    public function iChangeItsImageToPathForTheType(string $path, string $type): void
    {
        $this->imageFormElement->changeImageWithType($type, $path);
    }

    /**
     * @When I change the first image type to :type
     */
    public function iChangeTheFirstImageTypeTo(string $type): void
    {
        $this->imageFormElement->modifyFirstImageType($type);
    }

    /**
     * @Then /^(this taxon) should have only one image$/
     * @Then /^(this taxon) should(?:| still) have (\d+) images?$/
     */
    public function thereShouldStillBeOnlyOneImageInThisTaxon(TaxonInterface $taxon, int $count = 1): void
    {
        $this->iWantToModifyATaxon($taxon);

        Assert::same($this->imageFormElement->countImages(), (int) $count);
    }

    /**
     * @Then I should be notified that I cannot delete a menu taxon of any channel
     */
    public function iShouldBeNotifiedThatICannotDeleteAMenuTaxonOfAnyChannel(): void
    {
        $this->notificationChecker->checkNotification(
            'You cannot delete a menu taxon of any channel.',
            NotificationType::failure(),
        );
    }

    /**
     * @Then I should be notified that I cannot delete a taxon in use
     */
    public function iShouldBeNotifiedThatICannotDeleteATaxonInUse(): void
    {
        $this->notificationChecker->checkNotification(
            'Cannot delete, the Taxon is in use.',
            NotificationType::failure(),
        );
    }

    /**
     * @Then the first taxon on the list should be :taxonName
     */
    public function theFirstTaxonOnTheListShouldBe(string $taxonName): void
    {
        Assert::same($this->createPage->getFirstTaxonOnTheList(), $taxonName);
    }

    /**
     * @Then the last taxon on the list should be :taxonName
     */
    public function theLastTaxonOnTheListShouldBe(string $taxonName): void
    {
        Assert::same($this->createPage->getLastTaxonOnTheList(), $taxonName);
    }

    /**
     * @Then /^(?:this taxon|it) should be enabled$/
     */
    public function itShouldBeEnabled(): void
    {
        Assert::true($this->formElement->isEnabled());
    }

    /**
     * @Then /^(?:this taxon|it) should be disabled$/
     */
    public function itShouldBeDisabled(): void
    {
        Assert::false($this->formElement->isEnabled());
    }
}
