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
use Sylius\Behat\Page\Admin\Taxon\CreateForParentPageInterface;
use Sylius\Behat\Page\Admin\Taxon\CreatePageInterface;
use Sylius\Behat\Page\Admin\Taxon\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\Taxon;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingTaxonsContext implements Context
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
     * @var CreateForParentPageInterface
     */
    private $createForParentPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param CreatePageInterface $createPage
     * @param CreateForParentPageInterface $createForParentPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        CreatePageInterface $createPage,
        CreateForParentPageInterface $createForParentPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->createPage = $createPage;
        $this->createForParentPage = $createForParentPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @Given I want to create a new taxon
     * @Given I want to see all taxons in store
     */
    public function iWantToCreateANewTaxon()
    {
        $this->createPage->open();
    }

    /**
     * @Given I want to create a new taxon for :taxon
     */
    public function iWantToCreateANewTaxonForParent(TaxonInterface $taxon)
    {
        $this->createForParentPage->open(['id' => $taxon->getId()]);
    }

    /**
     * @Given /^I want to modify the ("[^"]+" taxon)$/
     */
    public function iWantToModifyATaxon(TaxonInterface $taxon)
    {
        $this->sharedStorage->set('taxon', $taxon);

        $this->updatePage->open(['id' => $taxon->getId()]);
    }

    /**
     * @When I specify its code as :code
     */
    public function iSpecifyItsCodeAs($code)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I name it :name in :language
     */
    public function iNameItIn($name, $language)
    {
        $this->createPage->nameIt($name, $language);
    }

    /**
     * @When I rename it to :name in :language
     */
    public function iRenameItIn($name, $language)
    {
        $this->updatePage->nameIt($name, $language);
    }

    /**
     * @When I set its slug to :slug
     */
    public function iSetItsSlugTo($slug)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([
            $this->createPage,
            $this->createForParentPage,
            $this->updatePage,
        ]);

        $currentPage->specifySlug($slug);
    }

    /**
     * @Then the slug field should not be editable
     */
    public function theSlugFieldShouldNotBeEditable()
    {
        Assert::true(
            $this->updatePage->isSlugReadOnly(),
            'Slug should be immutable, but it does not.'
        );
    }

    /**
     * @When I enable slug modification
     */
    public function iEnableSlugModification()
    {
        $this->updatePage->enableSlugModification();
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
     * @When /^I move up ("[^"]+" taxon)$/
     */
    public function iWantToMoveUpTaxon(TaxonInterface $taxon)
    {
        $this->createPage->moveUp($taxon);
    }

    /**
     * @When /^I move ("[^"]+" taxon) before ("[^"]+" taxon)$/
     */
    public function iMoveTaxonBeforeTaxon(TaxonInterface $taxonToMove, TaxonInterface $targetTaxon)
    {
        $this->createPage->insertBefore($taxonToMove, $targetTaxon);
    }

    /**
     * @Given /^I choose ("[^"]+" as a parent taxon)$/
     */
    public function iChooseAsAParentTaxon(TaxonInterface $taxon)
    {
        $this->createPage->chooseParent($taxon);
    }

    /**
     * @Given /^I change its (parent taxon to "[^"]+")$/
     */
    public function iChangeItsParentTaxonTo(TaxonInterface $taxon)
    {
        $this->updatePage->chooseParent($taxon);
    }

    /**
     * @When I do not specify its code
     */
    public function iDoNotSpecifyItsCode()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I do not specify its name
     */
    public function iDoNotSpecifyItsName()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I delete taxon named :name
     */
    public function iDeleteTaxonNamed($name)
    {
        $this->createPage->open();
        $this->createPage->deleteTaxonOnPageByName($name);
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
     * @Then /^the ("[^"]+" taxon) should appear in the registry$/
     */
    public function theTaxonShouldAppearInTheRegistry(TaxonInterface $taxon)
    {
        $this->updatePage->open(['id' => $taxon->getId()]);
        Assert::true(
            $this->updatePage->hasResourceValues(['code' => $taxon->getCode()]),
            sprintf('Taxon %s should be in the registry.', $taxon->getName())
        );
    }

    /**
     * @Then this taxon :element should be :value
     */
    public function thisTaxonElementShouldBe($element, $value)
    {
        Assert::true(
            $this->updatePage->hasResourceValues([$element => $value]),
            sprintf('Taxon with %s should have %s value.', $element, $value)
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code field should be disabled but it is not.'
        );
    }

    /**
     * @Then /^the slug of the ("[^"]+" taxon) should(?:| still) be "([^"]+)"$/
     */
    public function productSlugShouldBe(TaxonInterface $taxon, $slug)
    {
        $this->updatePage->open(['id' => $taxon->getId()]);

        Assert::true(
            $this->updatePage->hasResourceValues(['slug' => $slug]),
            sprintf('Taxon\'s slug should be %s.', $slug)
        );
    }

    /**
     * @Then /^this taxon should (belongs to "[^"]+")$/
     */
    public function thisTaxonShouldBelongsTo(TaxonInterface $taxon)
    {
        Assert::true(
            $this->updatePage->hasResourceValues(['parent' => $taxon->getId()]),
            sprintf('Current taxon should have %s parent taxon.', $taxon->getName())
        );
    }

    /**
     * @Given it should not belong to any other taxon
     */
    public function itShouldNotBelongToAnyOtherTaxon()
    {
        $parent = $this->updatePage->getParent();

        Assert::isEmpty(
            $parent,
            sprintf('Current taxon should not belong to any other, but it does belong to "%s"', $parent)
        );
    }

    /**
     * @Then I should be notified that taxon with this code already exists
     */
    public function iShouldBeNotifiedThatTaxonWithThisCodeAlreadyExists()
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage('code'), 'Taxon with given code already exists.');
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), sprintf('Please enter taxon %s.', $element));
    }

    /**
     * @Then /^there should still be only one taxon with code "([^"]+)"$/
     */
    public function thereShouldStillBeOnlyOneTaxonWithCode($code)
    {
        Assert::true(
            $this->updatePage->hasResourceValues(['code' => $code]),
            sprintf('Taxon with code %s cannot be found.', $code)
        );
    }

    /**
     * @Then /^Taxon named "([^"]+)" should not be added$/
     * @Then the taxon named :name should no longer exist in the registry
     */
    public function taxonNamedShouldNotBeAdded($name)
    {
        Assert::eq(
            0,
            $this->createPage->countTaxonsByName($name),
            sprintf('Taxon %s should not exist.', $name)
        );
    }

    /**
     * @Then /^I should see (\d+) taxons on the list$/
     */
    public function iShouldSeeTaxonsInTheList($number)
    {
        $taxonsOnPage = $this->createPage->countTaxons();

        Assert::same(
            (int) $number,
            $taxonsOnPage,
            sprintf('On list should be %d taxons but get %d.', $number, $taxonsOnPage)
        );
    }

    /**
     * @Then I should see the taxon named :name in the list
     */
    public function iShouldSeeTheTaxonNamedInTheList($name)
    {
        Assert::eq(
            1,
            $this->createPage->countTaxonsByName($name),
            sprintf('Taxon %s does not exist or multiple taxons with this name exist.', $name)
        );
    }

    /**
     * @When I attach the :path image with a code :code
     */
    public function iAttachImageWithACode($path, $code)
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->attachImage($path, $code);
    }

    /**
     * @When I attach the :path image without a code
     */
    public function iAttachImageWithoutACode($path)
    {
        $this->updatePage->attachImage($path);
    }

    /**
     * @Then /^this taxon should have(?:| also) an image with a code "([^"]*)"$/
     */
    public function thisTaxonShouldHaveAnImageWithCode($code)
    {
        Assert::true(
            $this->updatePage->isImageWithCodeDisplayed($code),
            sprintf('Image with a code %s should have been displayed.', $code)
        );
    }

    /**
     * @Then /^this taxon should not have(?:| also) an image with a code "([^"]*)"$/
     */
    public function thisTaxonShouldNotHaveAnImageWithCode($code)
    {
        Assert::false(
            $this->updatePage->isImageWithCodeDisplayed($code),
            sprintf('Image with a code %s should not have been displayed.', $code)
        );
    }

    /**
     * @When /^I remove(?:| also) an image with a code "([^"]*)"$/
     */
    public function iRemoveAnImageWithACode($code)
    {
        $this->updatePage->removeImageWithCode($code);
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
    public function thisTaxonShouldNotHaveImages(TaxonInterface $taxon)
    {
        $this->iWantToModifyATaxon($taxon);

        Assert::eq(
            0,
            $this->updatePage->countImages(),
            'This taxon has %2$s, but it should not have.'
        );
    }

    /**
     * @When I change the image with the :code code to :path
     */
    public function iChangeItsImageToPathForTheCode($path, $code)
    {
        $this->updatePage->changeImageWithCode($code, $path);
    }

    /**
     * @Then I should be notified that the image with this code already exists
     */
    public function iShouldBeNotifiedThatTheImageWithThisCodeAlreadyExists()
    {
        Assert::same($this->updatePage->getValidationMessageForImage(), 'Image code must be unique within this taxon.');
    }

    /**
     * @Then I should be notified that an image code is required
     */
    public function iShouldBeNotifiedThatAnImageCodeIsRequired()
    {
        Assert::same(
            $this->updatePage->getValidationMessageForImage(),
            'Please enter an image code.'
        );
    }

    /**
     * @Then there should still be only one image in the :taxon taxon
     */
    public function thereShouldStillBeOnlyOneImageInThisTaxon(TaxonInterface $taxon)
    {
        $this->iWantToModifyATaxon($taxon);

        Assert::eq(
            1,
            $this->updatePage->countImages(),
            'This taxon has %2$s images, but it should have only one.'
        );
    }

    /**
     * @Then the image code field should be disabled
     */
    public function theImageCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isImageCodeDisabled(),
            'Image code field should be disabled but it is not.'
        );
    }

    /**
     * @Then I should be notified that the :imageNumber image should have an unique code
     */
    public function iShouldBeNotifiedThatTheFirstImageShouldHaveAnUniqueCode($imageNumber)
    {
        preg_match_all('!\d+!', $imageNumber, $matches);

        Assert::same(
            $this->updatePage->getValidationMessageForImageAtPlace(((int) $matches[0][0]) - 1),
            'Image code must be unique within this taxon.'
        );
    }

    /**
     * @Then the first taxon on the list should be :taxon
     */
    public function theFirstTaxonOnTheListShouldBe(TaxonInterface $taxon)
    {
        Assert::same(
            $this->createPage->getFirstLeafName(),
            $taxon->getName(),
            sprintf(
                'Expected %s as a first taxon, but got %s.',
                $taxon->getName(),
                $this->createPage->getFirstLeafName()
            )
        );
    }

    /**
     * @Then they should have order like :firstTaxonName, :secondTaxonName, :thirdTaxonName and :fourthTaxonName
     */
    public function theyShouldHaveOrderLikeAnd(...$taxonsNames)
    {
        $leaves = $this->createPage->getLeaves();

        foreach ($leaves as $key => $leaf) {
            Assert::contains($taxonsNames[$key], $leaf->getText());
        }
    }
}
