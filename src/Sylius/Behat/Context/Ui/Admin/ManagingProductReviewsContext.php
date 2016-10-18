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
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ManagingProductReviewsContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @param IndexPageInterface $indexPage
     */
    public function __construct(IndexPageInterface $indexPage)
    {
        $this->indexPage = $indexPage;
    }

    /**
     * @When I want to browse product reviews
     */
    public function iWantToBrowseProductReviews()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should (also) see the product review :title in the list
     */
    public function iShouldSeeTheProductReviewTitleInTheList($title)
    {
        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['title' => $title]),
            sprintf('Product review with a title %s should exist but it does not.', $title)
        );
    }

    /**
     * @Then /^I should see (\d+) product reviews in the list$/
     */
    public function iShouldSeeProductReviewsInTheList($amount)
    {
        $foundRows = $this->indexPage->countItems();

        Assert::same(
            (int) $amount,
            $foundRows,
            '%2$s rows with product reviews should appear on page, %s rows has been found'
        );
    }
}
