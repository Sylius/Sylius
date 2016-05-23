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
use Sylius\Behat\Page\Admin\Taxon\CreatePageInterface;
use Sylius\Behat\Page\Admin\Taxon\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingTaxonsContext implements Context
{
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
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->createPage = $createPage;
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
     * @Given /^I want to modify the ("[^"]+" taxon)$/
     */
    public function iWantToModifyATaxon(TaxonInterface $taxon)
    {
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
     * @When I change its description to :description in :language
     */
    public function iChangeItsDescriptionToIn($description, $language)
    {
        $this->updatePage->describeItAs($description, $language);
    }

    /**
     * @When I specify its permalink as :permalink in :language
     */
    public function iSpecifyItsPermalinkAs($permalink, $language)
    {
        $this->createPage->specifyPermalink($permalink, $language);
    }

    /**
     * @When I change its permalink to :permalink in :language
     */
    public function iChangeItsPermalinkToIn($permalink, $language)
    {
        $this->updatePage->specifyPermalink($permalink, $language);
    }

    /**
     * @When I describe it as :description in :language
     */
    public function iDescribeItAs($description, $language)
    {
        $this->createPage->describeItAs($description, $language);
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
     * @Then I should be notified that taxon with this code already exists
     */
    public function iShouldBeNotifiedThatTaxonWithThisCodeAlreadyExists()
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor('code', 'Taxon with given code already exists.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, sprintf('Please enter taxon %s.', $element)),
            sprintf('I should be notified that taxon %s should be required.', $element)
        );
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

        Assert::eq(
            $number,
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
}
