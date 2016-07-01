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
use Sylius\Behat\Page\Admin\StaticContent\CreatePageInterface;
use Sylius\Behat\Page\Admin\StaticContent\UpdatePageInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ManagingStaticContentsContext implements Context
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
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
    }

    /**
     * @Given I want to create a new static content
     * @Given I want to add a new static content
     */
    public function iWantToCreateNewStaticContent()
    {
        $this->createPage->open();
    }

    /**
     * @When I set its title to :title
     */
    public function iSetItsTitleTo($title)
    {
        $this->createPage->setTitle($title);
    }

    /**
     * @When I set its internal name to :internalName
     */
    public function iSetItsInternalNameTo($internalName)
    {
        $this->createPage->setInternalName($internalName);
    }

    /**
     * @When I set its content to :content
     */
    public function iSetItsContentTo($content)
    {
        $this->createPage->setContent($content);
    }

    /**
     * @When I add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then the static content :title should appear in the store
     */
    public function theStaticContentShouldAppearInTheStore($title)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['title' => $title]),
            sprintf('Could not find static content with title "%s"!', $title)
        );
    }
}
