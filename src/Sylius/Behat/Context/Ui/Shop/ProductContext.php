<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @var ShowPageInterface
     */
    private $showPage;

    /**
     * @param ShowPageInterface $showPage
     */
    public function __construct(ShowPageInterface $showPage)
    {
        $this->showPage = $showPage;
    }

    /**
     * @Then I should be able to access product :product
     */
    public function iShouldBeAbleToAccessProduct(ProductInterface $product)
    {
        $this->showPage->tryToOpen(['slug' => $product->getSlug()]);

        Assert::true(
            $this->showPage->isOpen(['slug' => $product->getSlug()]),
            'Product show page should be open, but it does not.'
        );
    }

    /**
     * @Then I should not be able to access product :product
     */
    public function iShouldNotBeAbleToAccessProduct(ProductInterface $product)
    {
        $this->showPage->tryToOpen(['slug' => $product->getSlug()]);

        Assert::false(
            $this->showPage->isOpen(['slug' => $product->getSlug()]),
            'Product show page should not be open, but it does.'
        );
    }

    /**
     * @When /^I check (this product)'s details/
     */
    public function iOpenProductPage(ProductInterface $product)
    {
        $this->showPage->open(['slug' => $product->getSlug()]);
    }

    /**
     * @Given I should see the product name :name
     */
    public function iShouldSeeProductName($name)
    {
        Assert::same(
            $name,
            $this->showPage->getName(),
            'Product should have name %2$s, but it has %s'
        );
    }

    /**
     * @When I open page :url
     */
    public function iOpenPage($url)
    {
        $this->showPage->visit($url);
    }

    /**
     * @Then I should be on :product product detailed page
     */
    public function iShouldBeOnProductDetailedPage(ProductInterface $product)
    {
        Assert::true(
            $this->showPage->isOpen(['slug' => $product->getSlug()]),
            sprintf('Product %s show page should be open, but it does not.', $product->getName())
        );
    }

    /**
     * @Then I should see the product attribute :attributeName with value :AttributeValue
     */
    public function iShouldSeeTheProductAttributeWithValue($attributeName, $AttributeValue)
    {
        Assert::true(
            $this->showPage->isAttributeWithValueOnPage($attributeName, $AttributeValue),
            sprintf('Product should have attribute %s with value %s, but it does not.', $attributeName, $AttributeValue)
        );
    }
}
