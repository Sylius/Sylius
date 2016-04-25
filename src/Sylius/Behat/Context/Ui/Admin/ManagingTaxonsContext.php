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
use Sylius\Behat\Page\Admin\Taxon\CreatePageInterface;
use Sylius\Behat\Page\Admin\Taxon\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ManagingTaxonsContext implements Context
{
    const RESOURCE_NAME = 'taxon';

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
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @Given I want to create a new taxon
     */
    public function iWantToCreateANewTaxon()
    {
        $this->createPage->open();
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
     * @When I rename it to :name in :language
     */
    public function iNameItIn($name, $language)
    {
        $this->createPage->nameIt($name, $language);
    }

    /**
     * @When I specify its permalink as :permalink in :language
     */
    public function iSpecifyItsPermalinkAs($permalink, $language)
    {
        $this->createPage->specifyPermalink($permalink, $language);
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
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedItHasBeenSuccessfullyCreated()
    {
        $this->notificationChecker->checkCreationNotification(self::RESOURCE_NAME);
    }

    /**
     * @Then /^the ("[^"]+" taxon) should appear in the registry$/
     */
    public function theTaxonShouldAppearInTheRegistry(TaxonInterface $taxon)
    {
        $this->updatePage->open(['id' => $taxon->getId()]);
        Assert::true(
            $this->updatePage->hasResourceValues(['code' => $taxon->getCode()]),
            sprintf('Taxon %s should be in the registry', $taxon->getName())
        );
    }

    /**
     * @Then /^the ("[^"]+" taxon) with "([^"]*)" permalink and "([^"]*)" description should appear in the registry$/
     */
    public function theTaxonWithPermalinkAndDescriptionShouldAppearInTheRegistry(TaxonInterface $taxon, $permalink, $description)
    {
        $this->updatePage->open(['id' => $taxon->getId()]);
        Assert::true(
            $this->updatePage->hasResourceValues(['code' => $taxon->getCode(), 'permalink' => $permalink, 'description' => $description]),
            sprintf('Taxon %s should have %s permalink and %s description.', $taxon->getName(), $permalink, $description)
        );
    }

    /**
     * @Then /^the ("[^"]+" taxon) with ("[^"]+" parent taxon) should appear in the registry$/
     */
    public function theTaxonWithParentTaxonShouldAppearInTheRegistry(TaxonInterface $taxon, TaxonInterface $parentTaxon)
    {
        $this->updatePage->open(['id' => $taxon->getId()]);
        Assert::true(
            $this->updatePage->hasResourceValues(['parent' => $parentTaxon->getId()]),
            sprintf('Taxon %s should have %s parent taxon', $taxon->getName(), $parentTaxon->getName())
        );
    }
}
