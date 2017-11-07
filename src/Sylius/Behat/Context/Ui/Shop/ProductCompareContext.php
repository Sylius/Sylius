<?php

declare(strict_types=1);

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\ProductCompare\IndexPageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class ProductCompareContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    public function __construct(IndexPageInterface $indexPage)
    {
        $this->indexPage = $indexPage;
    }

    /**
     * @Given there is a product :product with :attribute attribute set to :value
     */
    public function iAddProductToCompare(ProductInterface $product, string $attribute, string $value)
    {

    }

    /**
     * @When I compare these products
     */
    public function iCompareProducts()
    {
        Assert::true($this->indexPage->tryToOpen());
    }

    /**
     * @Then I should see list of compared product attributes
     */
    public function iShouldSeeComparedAttributes()
    {
        Assert::eq($this->indexPage->getComparedAttributes(), 2);
    }
}
