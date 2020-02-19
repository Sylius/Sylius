<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Webmozart\Assert\Assert;

final class ManagingProductOptionsContext implements Context
{
    /** @var ApiClientInterface */
    private $client;

    public function __construct(ApiClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @When I browse product options
     */
    public function iBrowseProductOptions(): void
    {
        $this->client->index('product_options');
    }

    /**
     * @Then I should see :count product options in the list
     */
    public function iShouldSeeProductOptionsInTheList(int $count): void
    {
        $itemsCount = $this->client->countCollectionItems();

        Assert::eq($count, $itemsCount, sprintf('Expected %d product options, but got %d', $count, $itemsCount));
    }

    /**
     * @Then the product option :productOptionName should be in the registry
     */
    public function theProductOptionShouldAppearInTheRegistry(string $productOptionName): void
    {
        $this->client->index('product_options');
        $this->assertProductOptionWithData('name', $productOptionName);
    }

    private function assertProductOptionWithData(string $element, string $currencyName): void
    {
        $currencies = $this->client->getCollection();

        foreach ($currencies as $currency) {
            if ($currency[$element] === $currencyName) {
                return;
            }
        }

        throw new \Exception(sprintf('There is no product option with %s "%s" in the list', $element, $currencyName));
    }
}
