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
use Sylius\Behat\Page\Admin\SlideshowBlock\CreatePageInterface;
use Sylius\Behat\Page\Admin\SlideshowBlock\UpdatePageInterface;
use Sylius\Bundle\ContentBundle\Document\SlideshowBlock;
use Webmozart\Assert\Assert;

/**
 * @author Videni Videni <vidy.videni@gmail.com>
 */
final class ManagingSlideshowBlocksContext implements Context
{
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
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage
    )
    {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
    }

    /**
     * @Given I want to create a new slideshow block
     * @Given I want to add a new slideshow block
     */
    public function iWantToCreateNewSlideShow()
    {
        $this->createPage->open();
    }

    /**
     * @When I want to browse slideshow blocks of the store
     */
    public function iWantToBrowseSlideShowsOfTheStore()
    {
        $this->indexPage->open();
    }

    /**
     * @When I set its title to :title
     */
    public function iSetItsTitleTo($title)
    {
        $this->createPage->setTitle($title);
    }

    /**
     * @When I set its name to :name
     */
    public function iSetItsNameTo($name)
    {
        $this->createPage->setName($name);
    }

    /**
     * @When I make it published
     */
    public function iMakeItPublished()
    {
        $this->createPage->setPublished();
    }

    /**
     * @When I make it available from :startsDate to :endsDate
     */
    public function iMakeItAvailableFromTo(\DateTime $startsDate, \DateTime $endsDate)
    {
        $this->createPage->setPublishStartDate($startsDate);
        $this->createPage->setPublishEndDate($endsDate);
    }

    /**
     * @When I add a slide available from :startsDate to :endsDate
     */
    public function iAddASlide(\DateTime $startsDate, \DateTime $endsDate)
    {
        $this->createPage->addSlide($startsDate, $endsDate);
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
     * @Then /^I should be notified that (name|title) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        Assert::same(
            $this->createPage->getValidationMessage($element),
            'This value should not be blank.'
        );
    }

    /**
     * @Then the slideshow block :title should appear in the store
     * @Then I should see the slideshow block :title in the list
     */
    public function theSlideShowShouldAppearInTheStore($title)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['title' => $title]),
            sprintf('Could not find slideshow block with title "%s"!', $title)
        );
    }

    /**
     * @Then I should see :amount slideshow blocks in the list
     */
    public function iShouldSeeThatManySlideShowsInTheList($amount)
    {
        Assert::same(
            (int)$amount,
            $this->indexPage->countItems(),
            'Amount of slideshow blocks should be equal %s, but was %2$s.'
        );
    }

    /**
     * @Then the slideshow block :title should not be added
     */
    public function theSlideShowShouldNotBeAdded($title)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['title' => $title]),
            sprintf('Slideshow with title %s was created, but it should not.', $title)
        );
    }

    /**
     * @Given /^I want to edit (this slideshow block)$/
     */
    public function iWantToEditThisSlideShow(SlideshowBlock $slideshowBlock)
    {
        $this->updatePage->open(['id' => $slideshowBlock->getId()]);
    }

    /**
     * @When I change its title to :title
     */
    public function iChangeItsTitleTo($title)
    {
        $this->updatePage->changeTitleTo($title);
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
     * @When I delete slideshow block :title
     */
    public function iDeleteSlideShow($title)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['title' => $title]);
    }

    /**
     * @Then the slideshow block :title should no longer exist in the store
     */
    public function theSlideShowShouldNoLongerExistInTheStore($title)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['title' => $title]),
            sprintf('Slideshow with title %s exists, but should not.', $title)
        );
    }


    /**
     * @Then /^(this slideshow block) should have title "([^"]+)"$/
     */
    public function thisStaticContentShouldHaveBody(SlideshowBlock $slideshowBlock, $title)
    {
        $this->updatePage->open(['id' => $slideshowBlock->getId()]);

        Assert::same($this->updatePage->getTitle(), $title);
    }
}
