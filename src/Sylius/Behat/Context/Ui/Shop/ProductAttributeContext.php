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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Page\Shop\Product\ShowPageInterface;
use Webmozart\Assert\Assert;

final class ProductAttributeContext implements Context
{
    public function __construct(private ShowPageInterface $showPage)
    {
    }

    /**
     * @Then I should (also) see the product attribute :attributeName with value :expectedAttribute
     */
    public function iShouldSeeTheProductAttributeWithValue(string $attributeName, string $expectedAttribute): void
    {
        Assert::same($this->showPage->getAttributeByName($attributeName), $expectedAttribute);
    }

    /**
     * @Then /^I should(?:| also) see the product attribute "([^"]+)" with (positive|negative) value$/
     */
    public function iShouldSeeTheProductAttributeWithBoolean(string $attributeName, string $expectedAttribute): void
    {
        Assert::same($this->showPage->getAttributeByName($attributeName), 'positive' === $expectedAttribute ? 'Yes' : 'No');
    }

    /**
     * @Then I should (also) see the product attribute :attributeName with value :expectedAttribute on the list
     */
    public function iShouldSeeTheProductAttributeWithValueOnTheList(string $attributeName, string $expectedAttribute): void
    {
        Assert::inArray($expectedAttribute, $this->showPage->getAttributeListByName($attributeName));
    }

    /**
     * @Then I should not see the product attribute :attributeName
     */
    public function iShouldNotSeeTheProductAttribute(string $attributeName): void
    {
        $this->showPage->getAttributeByName($attributeName);
    }

    /**
     * @Then I should (also) see the product attribute :attributeName with date :expectedAttribute
     */
    public function iShouldSeeTheProductAttributeWithDate(string $attributeName, string $expectedAttribute): void
    {
        Assert::eq(
            new \DateTime($this->showPage->getAttributeByName($attributeName)),
            new \DateTime($expectedAttribute),
        );
    }

    /**
     * @Then /^I should(?:| also) see the product attribute "([^"]+)" with value ([^"]+)%$/
     */
    public function iShouldSeeTheProductAttributeWithPercentage(string $attributeName, int $expectedAttribute): void
    {
        Assert::eq(
            $this->showPage->getAttributeByName($attributeName),
            sprintf('%d %%', $expectedAttribute),
        );
    }

    /**
     * @Then I should see :count attributes
     */
    public function iShouldSeeAttributes(int $count): void
    {
        Assert::count($this->getProductAttributes(), $count);
    }

    /**
     * @Then the first attribute should be :name
     */
    public function theFirstAttributeShouldBe(string $name): void
    {
        $attributes = $this->getProductAttributes();

        Assert::same(reset($attributes)->getText(), $name);
    }

    /**
     * @Then the last attribute should be :name
     */
    public function theLastAttributeShouldBe(string $name): void
    {
        $attributes = $this->getProductAttributes();

        Assert::same(end($attributes)->getText(), $name);
    }

    /**
     * @return NodeElement[]
     *
     * @throws \InvalidArgumentException
     */
    private function getProductAttributes(): array
    {
        $attributes = $this->showPage->getAttributes();
        Assert::notNull($attributes, 'The product has no attributes.');

        return $attributes;
    }
}
