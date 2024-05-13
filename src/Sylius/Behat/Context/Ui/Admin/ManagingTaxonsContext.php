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
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Behat\Context\Ui\Admin\Helper\ValidationTrait;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Behat\Page\Admin\Taxon\CreateForParentPageInterface;
use Sylius\Behat\Page\Admin\Taxon\CreatePageInterface;
use Sylius\Behat\Page\Admin\Taxon\UpdatePageInterface;
use Sylius\Behat\Service\Helper\JavaScriptTestHelper;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ManagingTaxonsContext implements Context
{
    use ValidationTrait;

    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private CreatePageInterface $createPage,
        private CreateForParentPageInterface $createForParentPage,
        private UpdatePageInterface $updatePage,
        private CurrentPageResolverInterface $currentPageResolver,
        private NotificationCheckerInterface $notificationChecker,
        private JavaScriptTestHelper $testHelper,
        private UpdateSimpleProductPageInterface $updateSimpleProductPage,
    ) {
    }

    /**
     * @When I want to create a new taxon
     * @When I want to see all taxons in store
     */
    public function iWantToCreateANewTaxon()
    {
        $this->createPage->open();
    }

    /**
     * @When I want to create a new taxon for :taxon
     */
    public function iWantToCreateANewTaxonForParent(TaxonInterface $taxon)
    {
        $this->testHelper->waitUntilPageOpens($this->createForParentPage, ['id' => $taxon->getId()]);
    }

    /**
     * @When /^I want to modify the ("[^"]+" taxon)$/
     */
    public function iWantToModifyATaxon(TaxonInterface $taxon)
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
        $this->createPage->specifyCode($code ?? '');
    }

    /**
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     * @When I do not specify its name
     */
    public function iNameItIn(?string $name = null, $language = 'en_US')
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->nameIt($name ?? '', $language);
    }

    /**
     * @When I set its slug to :slug
     * @When I do not specify its slug
     * @When I set its slug to :slug in :language
     */
    public function iSetItsSlugToIn(?string $slug = null, $language = 'en_US')
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->specifySlug($slug ?? '', $language);
    }

    /**
     * @Then the slug field should not be editable
     * @Then the slug field should (also )not be editable in :language
     */
    public function theSlugFieldShouldNotBeEditable($language = 'en_US')
    {
        Assert::true($this->updatePage->isSlugReadonly($language));
    }

    /**
     * @When I enable slug modification
     * @When I enable slug modification in :language
     */
    public function iEnableSlugModification($language = 'en_US')
    {
        $this->updatePage->activateLanguageTab($language);
        $this->updatePage->enableSlugModification($language);
    }

    /**
     * @When I change its description to :description in :language
     */
    public function iChangeItsDescriptionToIn($description, $language)
    {
        $this->updatePage->describeItAs($description, $language);
    }

    /**
     * @When I describe it as :description in :language
     */
    public function iDescribeItAs($description, $language)
    {
        $this->createPage->describeItAs($description, $language);
    }

    /**
     * @Given /^I set its (parent taxon to "[^"]+")$/
     * @Given /^I change its (parent taxon to "[^"]+")$/
     * @Then /^I should be able to change its (parent taxon to "[^"]+")$/
     */
    public function iChangeItsParentTaxonTo(TaxonInterface $taxon)
    {
        $this->updatePage->chooseParent($taxon);
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
     * @When I save my changes to the images
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @Then /^the ("[^"]+" taxon) should appear in the registry$/
     */
    public function theTaxonShouldAppearInTheRegistry(TaxonInterface $taxon)
    {
        $this->updatePage->open(['id' => $taxon->getId()]);
        Assert::true($this->updatePage->hasResourceValues(['code' => $taxon->getCode()]));
    }

    /**
     * @Then this taxon :element should be :value
     */
    public function thisTaxonElementShouldBe($element, $value)
    {
        Assert::true($this->updatePage->hasResourceValues([$element => $value]));
    }

    /**
     * @Then this taxon should have slug :value in :language
     */
    public function thisTaxonElementShouldHaveSlugIn($value, $language = null)
    {
        if (null !== $language) {
            $this->updatePage->activateLanguageTab($language);
        }

        Assert::same($this->updatePage->getSlug($language ?? ''), $value);
    }

    /**
     * @Then I should not be able to edit its code
     */
    public function iShouldNotBeAbleToEditItsCode(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then /^the slug of the ("[^"]+" taxon) should(?:| still) be "([^"]+)"$/
     */
    public function productSlugShouldBe(TaxonInterface $taxon, $slug)
    {
        $this->updatePage->open(['id' => $taxon->getId()]);

        Assert::same($this->updatePage->getSlug(), $slug);
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
    public function thisTaxonShouldBelongsTo(TaxonInterface $taxon)
    {
        Assert::true($this->updatePage->hasResourceValues(['parent' => $taxon->getCode()]));
    }

    /**
     * @Given it should not belong to any other taxon
     */
    public function itShouldNotBelongToAnyOtherTaxon()
    {
        Assert::isEmpty($this->updatePage->getParent());
    }

    /**
     * @Then I should be notified that taxon with this code already exists
     */
    public function iShouldBeNotifiedThatTaxonWithThisCodeAlreadyExists()
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->getValidationMessage('code'), 'Taxon with given code already exists.');
    }

    /**
     * @Then I should be notified that taxon slug must be unique
     */
    public function iShouldBeNotifiedThatTaxonSlugMustBeUnique()
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->getValidationMessage('slug'), 'Taxon slug must be unique.');
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $currentPage = $this->resolveCurrentPage();

        Assert::same($currentPage->getValidationMessage($element), sprintf('Please enter taxon %s.', $element));
    }

    /**
     * @Then /^there should(?:| still) be only one taxon with code "([^"]+)"$/
     */
    public function thereShouldStillBeOnlyOneTaxonWithCode($code)
    {
        Assert::true($this->updatePage->hasResourceValues(['code' => $code]));
    }

    /**
     * @Then /^taxon named "([^"]+)" should not be added$/
     * @Then the taxon named :name should no longer exist in the registry
     */
    public function taxonNamedShouldNotBeAdded($name)
    {
        if (!$this->createPage->isOpen()) {
            $this->createPage->open();
        }

        Assert::same($this->createPage->countTaxonsByName($name), 0);
    }

    /**
     * @Then /^I should see (\d+) taxons on the list$/
     */
    public function iShouldSeeTaxonsInTheList($number)
    {
        Assert::same($this->createPage->countTaxons(), (int) $number);
    }

    /**
     * @When I attach the :path image with :type type
     * @When I attach the :path image with :type type to this taxon
     * @When I attach the :path image
     * @When I attach the :path image to this taxon
     */
    public function iAttachImageWithType(string $path, ?string $type = null): void
    {
        $currentPage = $this->resolveCurrentPage();

        $currentPage->attachImage($path, $type);
    }

    /**
     * @Then I should see the taxon named :name in the list
     */
    public function iShouldSeeTheTaxonNamedInTheList($name)
    {
        Assert::same($this->createPage->countTaxonsByName($name), 1);
    }

    /**
     * @Then /^(?:it|this taxon) should(?:| also) have an image with "([^"]*)" type$/
     */
    public function thisTaxonShouldHaveAnImageWithType(string $type): void
    {
        Assert::true($this->updatePage->isImageWithTypeDisplayed($type));
    }

    /**
     * @Then /^(?:this taxon|it) should not have(?:| also) any images with "([^"]*)" type$/
     */
    public function thisTaxonShouldNotHaveAnImageWithType($code)
    {
        Assert::false($this->updatePage->isImageWithTypeDisplayed($code));
    }

    /**
     * @When /^I(?:| also) remove an image with "([^"]*)" type$/
     */
    public function iRemoveAnImageWithType(string $type): void
    {
        $this->updatePage->removeImageWithType($type);
    }

    /**
     * @When I remove the first image
     */
    public function iRemoveTheFirstImage()
    {
        $this->updatePage->removeFirstImage();
    }

    /**
     * @Then /^(this taxon) should not have any images$/
     */
    public function thisTaxonShouldNotHaveAnyImages(TaxonInterface $taxon)
    {
        $this->iWantToModifyATaxon($taxon);

        Assert::same($this->updatePage->countImages(), 0);
    }

    /**
     * @When I change the image with the :type type to :path
     */
    public function iChangeItsImageToPathForTheType($path, $type)
    {
        $this->updatePage->changeImageWithType($type, $path);
    }

    /**
     * @When I change the first image type to :type
     */
    public function iChangeTheFirstImageTypeTo($type)
    {
        $this->updatePage->modifyFirstImageType($type);
    }

    /**
     * @Then /^(this taxon) should have only one image$/
     * @Then /^(this taxon) should(?:| still) have (\d+) images?$/
     */
    public function thereShouldStillBeOnlyOneImageInThisTaxon(TaxonInterface $taxon, $count = 1)
    {
        $this->iWantToModifyATaxon($taxon);

        Assert::same($this->updatePage->countImages(), (int) $count);
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
     * @When I move up :taxonName taxon
     */
    public function iMoveUpTaxon(string $taxonName)
    {
        $this->createPage->moveUpTaxon($taxonName);
    }

    /**
     * @When I move down :taxonName taxon
     */
    public function iMoveDownTaxon(string $taxonName)
    {
        $this->createPage->moveDownTaxon($taxonName);
    }

    /**
     * @Then the first taxon on the list should be :taxonName
     */
    public function theFirstTaxonOnTheListShouldBe(string $taxonName)
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
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->updatePage->disable();
    }

    /**
     * @Then /^(?:this taxon|it) should be enabled$/
     */
    public function itShouldBeEnabled(): void
    {
        Assert::true($this->updatePage->isEnabled());
    }

    /**
     * @Then /^(?:this taxon|it) should be disabled$/
     */
    public function itShouldBeDisabled(): void
    {
        Assert::false($this->updatePage->isEnabled());
    }

    private function resolveCurrentPage(): CreateForParentPageInterface|CreatePageInterface|SymfonyPageInterface|UpdatePageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
            $this->createForParentPage,
            $this->updatePage,
        ]);
    }
}
